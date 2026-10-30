
<script setup lang="ts">
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

interface League {
    id: number;
    name: string;
    system_name: string;
    started_at: string | null;
}

interface Category {
    id: number;
    name: string;
    system_name: string;
    image_path: string | null;
}

interface Item {
    id: number;
    name: string;
    image_path: string | null;
    category_id: number;
    lowest_price: string | number | null;
    category?: Category | null;
}

interface PaginatedItems {
    data: Item[];
    links: {
        next: string | null;
        prev: string | null;
    };
    meta: {
        current_page: number;
    };
}

const props = defineProps<{
    leagues: League[];
    selectedLeagueId: number | null;
    categories: Category[];
    items: PaginatedItems;
    filters: {
        category: number | null;
        search: string;
    };
}>();

const selectedCategoryId = ref<number | null>(
    props.filters.category ?? null,
);
const search = ref(props.filters.search ?? '');
const items = ref<Item[]>(props.items.data);
const page = ref(props.items.current_page);
const hasMore = ref(props.items.current_page < props.items.last_page);
const loading = ref(false);
const loadingMore = ref(false);
const errorMessage = ref('');
const scrollSentinel = ref<HTMLElement | null>(null);

let observer: IntersectionObserver | null = null;
let searchTimeout: ReturnType<typeof setTimeout> | undefined;
let requestVersion = 0;

const selectedLeague = computed(
    () => props.leagues.find(
        (league) => league.id === props.selectedLeagueId,
    ),
);

function imageUrl(path: string | null | undefined): string | null {
    if (!path) return null;

    if (/^https?:\/\//i.test(path)) return path;

    return path.startsWith('/') ? path : `/storage/${path}`;
}

function formatPrice(price: string | number | null): string {
    if (price === null || price === '') return '—';

    return new Intl.NumberFormat('en-US', {
        maximumFractionDigits: 2,
    }).format(Number(price));
}

function navigateToItem(item: Item) {
    router.get(`/items/${item.id}`, {league: props.selectedLeagueId ?? undefined});
}

function applyFilters() {
    router.get(
        '/',
        {
            league: props.selectedLeagueId ?? undefined,
            category: selectedCategoryId.value ?? undefined,
            search: search.value.trim() || undefined,
        },
        {
            preserveState: false,
            preserveScroll: true,
            replace: true,
        },
    );
}

function selectCategory(categoryId: number | null) {
    selectedCategoryId.value = categoryId;
    applyFilters();
}

function selectLeague(event: Event) {
    const target = event.target as HTMLSelectElement;
    const leagueId = Number(target.value);

    router.get(
        '/',
        {
            league: leagueId,
            category: selectedCategoryId.value ?? undefined,
            search: search.value.trim() || undefined,
        },
        {
            preserveState: false,
            preserveScroll: false,
        },
    );
}

async function loadItems(reset = false) {
    if (loadingMore.value || (loading.value && !reset)) return;
    if (!reset && !hasMore.value) return;

    const version = ++requestVersion;

    if (reset) {
        loading.value = true;
        items.value = [];
        page.value = 0;
        hasMore.value = true;
    } else {
        loadingMore.value = true;
    }

    errorMessage.value = '';

    const params = new URLSearchParams({
        league: String(props.selectedLeagueId ?? ''),
        page: String(reset ? 1 : page.value + 1),
    });

    if (selectedCategoryId.value) {
        params.set('category', String(selectedCategoryId.value));
    }

    if (search.value.trim()) {
        params.set('search', search.value.trim());
    }

    try {
        const response = await fetch(`/catalog/items?${params}`, {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Could not load items.');
        }

        const result = await response.json();

        if (version !== requestVersion) return;

        items.value = reset
            ? result.data
            : [...items.value, ...result.data];

        page.value = result.meta.current_page;
        hasMore.value = Boolean(result.links.next);
    } catch {
        if (version === requestVersion) {
            errorMessage.value = 'Unable to load items. Please try again.';
        }
    } finally {
        if (version === requestVersion) {
            loading.value = false;
            loadingMore.value = false;
        }
    }
}

function retryLoading() {
    void loadItems(false);
}

watch(search, () => {
    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 350);
});

watch(scrollSentinel, (element, previousElement) => {
    if (previousElement) {
        observer?.unobserve(previousElement);
    }

    if (element) {
        observer?.observe(element);
    }
});

onMounted(() => {
    observer = new IntersectionObserver(
        (entries) => {
            if (entries.some((entry) => entry.isIntersecting)) {
                void loadItems(false);
            }
        },
        { rootMargin: '300px' },
    );
});

onUnmounted(() => {
    observer?.disconnect();

    if (searchTimeout) clearTimeout(searchTimeout);
});
</script>

<template>
    <div
        class="grid grid-cols-1 gap-6 lg:grid-cols-[250px_minmax(0,1fr)]"
    >
        <aside>
                <div class="mb-3 flex items-center justify-between">
                    <h2
                        class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500"
                    >
                        Categories
                    </h2>
                    <span class="text-xs text-slate-600">
                        {{ categories.length }}
                    </span>
                </div>

                <nav
                    class="flex gap-2 overflow-x-auto pb-2 lg:flex-col lg:overflow-visible"
                    aria-label="Item categories"
                >
                    <button
                        type="button"
                        class="flex shrink-0 items-center gap-3 rounded-xl border px-3 py-3 text-left text-sm transition"
                        :class="
                            selectedCategoryId === null
                                ? 'border-amber-400/40 bg-amber-400/10 text-amber-200'
                                : 'border-transparent bg-slate-900 text-slate-300 hover:bg-slate-800'
                        "
                        @click="selectCategory(null)"
                    >
                        <img
                            v-if="imageUrl('images/categories/UI_Img_AuctionHouseV2_Icon01_Jihuo.png')"
                            :src="imageUrl('images/categories/UI_Img_AuctionHouseV2_Icon01_Jihuo.png')!"
                            alt="All items"
                            class="h-9 w-9 rounded-lg bg-slate-800 object-contain p-1"
                            loading="lazy"
                        />
                        <span class="font-medium">All items</span>
                    </button>

                    <button
                        v-for="category in categories"
                        :key="category.id"
                        type="button"
                        class="flex shrink-0 items-center gap-3 rounded-xl border px-3 py-3 text-left text-sm transition lg:w-full"
                        :class="
                            selectedCategoryId === category.id
                                ? 'border-amber-400/40 bg-amber-400/10 text-amber-200'
                                : 'border-transparent bg-slate-900 text-slate-300 hover:bg-slate-800'
                        "
                        @click="selectCategory(category.id)"
                    >
                        <img
                            v-if="imageUrl(category.image_path)"
                            :src="imageUrl(category.image_path)!"
                            :alt="category.name"
                            class="h-9 w-9 rounded-lg bg-slate-800 object-contain p-1"
                            loading="lazy"
                        />
                        <span
                            v-else
                            class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-800 text-slate-500"
                        >
                            ◈
                        </span>
                        <span class="whitespace-nowrap font-medium">
                            {{ category.name }}
                        </span>
                    </button>
                </nav>
            </aside>

        <section class="min-w-0">
                <div
                    class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
                >
                    <div>
                        <p class="mb-1 text-sm text-slate-500">
                            {{ selectedLeague?.name ?? 'No league selected' }}
                        </p>
                        <h2 class="text-2xl font-bold tracking-tight">
                            {{
                                categories.find(
                                    (category) =>
                                        category.id === selectedCategoryId,
                                )?.name ?? 'All items'
                            }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Browse items and their latest lowest prices.
                        </p>
                    </div>
                </div>

                <div class="relative mb-4">
                    <span
                        class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500"
                        aria-hidden="true"
                    >
                        ⌕
                    </span>
                    <input
                        v-model="search"
                        type="search"
                        placeholder="Search items by name..."
                        aria-label="Search items by name"
                        class="w-full rounded-xl border border-slate-800 bg-slate-900 py-3.5 pl-11 pr-4 text-sm text-slate-100 outline-none placeholder:text-slate-600 focus:border-amber-400/60 focus:ring-2 focus:ring-amber-400/10"
                    />
                </div>

                <div
                    class="mb-2 hidden grid-cols-[minmax(0,1fr)_140px] px-4 py-2 text-xs font-semibold uppercase tracking-wider text-slate-500 sm:grid"
                >
                    <span>Item</span>
                    <span class="text-right">Lowest price</span>
                </div>

                <div
                    v-if="!leagues.length"
                    class="rounded-xl border border-slate-800 bg-slate-900 p-8 text-center"
                >
                    <h3 class="font-semibold">No leagues available</h3>
                    <p class="mt-2 text-sm text-slate-500">
                        Add a league to start browsing market data.
                    </p>
                </div>

                <div
                    v-else-if="!items.length && !loading"
                    class="rounded-xl border border-slate-800 bg-slate-900 p-10 text-center"
                >
                    <p class="font-medium">No items found</p>
                    <p class="mt-2 text-sm text-slate-500">
                        Try another search or category.
                    </p>
                </div>

                <div v-else class="space-y-2">
                    <button
                        v-for="item in items"
                        :key="item.id"
                        type="button"
                        class="grid w-full grid-cols-[minmax(0,1fr)_auto] items-center gap-3 rounded-xl border border-slate-800/90 bg-slate-900 p-3 text-left transition hover:border-slate-700 hover:bg-slate-800/80 sm:grid-cols-[minmax(0,1fr)_140px] sm:gap-4 sm:p-4"
                        @click="navigateToItem(item)"
                    >
                        <span class="flex min-w-0 items-center gap-3">
                            <img
                                v-if="imageUrl(item.image_path)"
                                :src="imageUrl(item.image_path)!"
                                :alt="item.name"
                                class="h-12 w-12 shrink-0 rounded-lg border border-slate-700/70 bg-slate-800 object-contain p-1"
                                loading="lazy"
                            />
                            <span
                                v-else
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg border border-slate-700/70 bg-slate-800 text-xl text-slate-500"
                            >
                                ◈
                            </span>

                            <span class="min-w-0">
                                <span
                                    class="block truncate text-sm font-medium text-slate-100"
                                >
                                    {{ item.name }}
                                </span>
                                <span
                                    class="mt-1 block text-xs text-slate-500"
                                >
                                    {{ item.category?.name ?? 'Item' }}
                                </span>
                            </span>
                        </span>

                        <span class="text-right">
                            <span
                                class="block text-sm font-semibold tabular-nums"
                                :class="
                                    item.lowest_price == null
                                        ? 'text-slate-500'
                                        : 'text-amber-300'
                                "
                            >
                                {{ formatPrice(item.lowest_price) }}
                            </span>
                            <span
                                class="mt-1 block text-xs text-slate-500"
                            >
                                {{ item.lowest_price == null ? 'No data' : 'Lowest' }}
                            </span>
                        </span>
                    </button>
                </div>

                <div
                    v-if="loading"
                    class="py-6 text-center text-sm text-slate-400"
                    role="status"
                >
                    Loading items…
                </div>

                <div
                    v-if="errorMessage"
                    class="mt-4 rounded-xl border border-red-900/70 bg-red-950/30 p-4 text-sm text-red-300"
                    role="alert"
                >
                    <p>{{ errorMessage }}</p>
                    <button
                        type="button"
                        class="mt-2 font-semibold underline"
                        @click="retryLoading"
                    >
                        Try again
                    </button>
                </div>

                <div
                    v-if="loadingMore"
                    class="py-5 text-center text-sm text-slate-500"
                    role="status"
                >
                    Loading more items…
                </div>

                <div
                    v-if="hasMore && !loading"
                    ref="scrollSentinel"
                    class="h-8"
                    aria-hidden="true"
                />

                <p
                    v-if="!hasMore && items.length"
                    class="py-6 text-center text-xs text-slate-600"
                >
                    You've reached the end of the list.
                </p>
            </section>
    </div>
</template>
