
<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';

interface League {
    id: number;
    name: string;
    system_name: string;
    started_at: string | null;
}

interface SharedProps {
    [key: string]: unknown;
    leagues: League[];
    selectedLeagueId: number | null;
}

const page = usePage<SharedProps>();

function selectLeague(event: Event) {
    const target = event.target as HTMLSelectElement;
    const leagueId = Number(target.value);

    const currentUrl = new URL(page.url, window.location.origin);

    router.get(
        currentUrl.pathname,
        {
            ...Object.fromEntries(currentUrl.searchParams.entries()),
            league: leagueId,
        },
        {
            preserveScroll: false,
            preserveState: false,
        },
    );
}
</script>

<template>
    <header class="border-b border-slate-800 bg-slate-900/90">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8"
        >
            <a href="/" class="flex items-center gap-3">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-xl border border-amber-400/20 bg-amber-400/10 text-xl text-amber-300"
                >
                    ⚔
                </div>

                <div>
                    <h1 class="text-xl font-bold tracking-tight">
                        TLI Market Insights
                    </h1>

                    <p
                        class="mt-0.5 text-xs font-medium uppercase tracking-[0.2em] text-slate-500"
                    >
                        Trade house analytics
                    </p>
                </div>
            </a>

            <label class="flex items-center gap-3">
                <span class="text-sm text-slate-400">League</span>

                <select
                    :value="page.props.selectedLeagueId ?? ''"
                    :disabled="!page.props.leagues?.length"
                    class="min-w-48 rounded-lg border border-slate-700 bg-slate-800 px-3 py-2.5 text-sm text-slate-100 outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-400/20"
                    @change="selectLeague"
                >
                    <option
                        v-for="league in page.props.leagues ?? []"
                        :key="league.id"
                        :value="league.id"
                    >
                        {{ league.name }}
                    </option>
                </select>
            </label>
        </div>
    </header>
</template>
