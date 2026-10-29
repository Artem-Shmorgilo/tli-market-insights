<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';

defineOptions({
    layout: AppLayout,
});

interface Item {
    id: number;
    name: string;
    external_id: string;
    image_path: string | null;
}

defineProps<{
    item: Item;
}>();

function imageUrl(path: string | null): string | null {
    if (!path) return null;
    if (/^https?:\/\//i.test(path)) return path;
    return path.startsWith('/') ? path : `/storage/${path}`;
}
</script>

<template>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">
            <section
                class="rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8"
            >
                <div class="flex items-start gap-4">
                    <img
                        v-if="imageUrl(item.image_path)"
                        :src="imageUrl(item.image_path)!"
                        :alt="item.name"
                        class="h-20 w-20 rounded-xl border border-slate-700 bg-slate-800 object-contain p-2"
                    />

                    <div
                        v-else
                        class="flex h-20 w-20 items-center justify-center rounded-xl bg-slate-800 text-3xl text-slate-500"
                    >
                        ◈
                    </div>

                    <div class="min-w-0">
                        <p
                            class="text-xs font-semibold uppercase tracking-widest text-slate-500"
                        >
                            Item details
                        </p>
                        <h1 class="mt-2 text-2xl font-bold sm:text-3xl">
                            {{ item.name }}
                        </h1>
                        <p class="mt-2 text-sm text-slate-500">
                            External ID: {{ item.external_id }}
                        </p>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-800 pt-6">
                    <h2 class="text-lg font-semibold">Price history</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Price charts and market statistics will be added here.
                    </p>
                </div>
            </section>
        </main>
    </div>
</template>
