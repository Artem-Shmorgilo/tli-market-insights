
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    message: string;
}>();

const features = [
    {
        number: '01',
        title: 'Item catalog',
        description:
            'Browse items by category and quickly find the equipment you need.',
        icon: '◈',
    },
    {
        number: '02',
        title: 'Price history',
        description:
            'Follow price changes over time and compare market trends.',
        icon: '↗',
    },
    {
        number: '03',
        title: 'League tracking',
        description:
            'Keep market data organized by league and season.',
        icon: '◎',
    },
];
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen overflow-hidden bg-slate-950 text-slate-100">
        <!-- Background decoration -->
        <div
            class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(124,58,237,0.16),_transparent_45%)]"
        ></div>

        <!-- Navigation -->
        <header class="relative border-b border-white/10">
            <nav
                class="mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-8"
            >
                <a href="/" class="flex items-center gap-3">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-500 text-xl font-bold text-white shadow-lg shadow-violet-500/25"
                    >
                        T
                    </span>
                    <span class="text-lg font-semibold tracking-tight">
                        TLI <span class="text-violet-400">Market Insights</span>
                    </span>
                </a>

                <div class="hidden items-center gap-8 text-sm text-slate-400 sm:flex">
                    <a href="#features" class="transition hover:text-white">
                        Features
                    </a>
                    <a href="#about" class="transition hover:text-white">
                        About
                    </a>
                    <a
                        href="#features"
                        class="rounded-lg bg-white px-4 py-2.5 font-semibold text-slate-950 transition hover:bg-violet-200"
                    >
                        Explore market
                    </a>
                </div>
            </nav>
        </header>

        <main class="relative">
            <!-- Hero -->
            <section
                class="mx-auto grid max-w-7xl items-center gap-14 px-6 py-20 sm:py-28 lg:grid-cols-2 lg:px-8 lg:py-32"
            >
                <div>
                    <div
                        class="mb-8 inline-flex items-center gap-2 rounded-full border border-violet-400/20 bg-violet-400/10 px-4 py-2 text-sm text-violet-300"
                    >
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        Built for Torchlight: Infinite
                    </div>

                    <h1
                        class="max-w-2xl text-5xl font-bold tracking-tight sm:text-6xl lg:text-7xl"
                    >
                        Understand the market.
                        <span
                            class="mt-2 block bg-gradient-to-r from-violet-400 to-cyan-300 bg-clip-text text-transparent"
                        >
                            Find your edge.
                        </span>
                    </h1>

                    <p class="mt-7 max-w-xl text-lg leading-8 text-slate-400">
                        Track item prices, explore market trends, and compare
                        values across leagues — all in one place.
                    </p>

                    <div class="mt-10 flex flex-wrap gap-4">
                        <a
                            href="#features"
                            class="inline-flex items-center gap-2 rounded-xl bg-violet-600 px-6 py-3.5 font-semibold text-white shadow-lg shadow-violet-600/25 transition hover:bg-violet-500"
                        >
                            Explore features
                            <span aria-hidden="true">→</span>
                        </a>

                        <a
                            href="#about"
                            class="rounded-xl border border-white/15 px-6 py-3.5 font-semibold text-slate-200 transition hover:border-white/30 hover:bg-white/5"
                        >
                            Learn more
                        </a>
                    </div>

                    <p class="mt-6 text-sm text-slate-500">
                        {{ message }}
                    </p>
                </div>

                <!-- Decorative market preview -->
                <div class="relative mx-auto w-full max-w-lg">
                    <div
                        class="absolute -inset-8 rounded-full bg-violet-600/10 blur-3xl"
                    ></div>

                    <div
                        class="relative rounded-2xl border border-white/10 bg-slate-900/90 p-6 shadow-2xl shadow-black/30"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-slate-400">
                                    Market overview
                                </p>
                                <h2 class="mt-1 text-xl font-semibold">
                                    Price trends
                                </h2>
                            </div>

                            <span
                                class="rounded-lg border border-emerald-400/20 bg-emerald-400/10 px-3 py-1.5 text-xs font-medium text-emerald-300"
                            >
                                Demo preview
                            </span>
                        </div>

                        <div
                            class="mt-8 flex h-40 items-end gap-3 border-b border-l border-white/10 px-3"
                            aria-label="Illustrative price trend chart"
                        >
                            <div class="h-[30%] flex-1 rounded-t-md bg-violet-500/40"></div>
                            <div class="h-[45%] flex-1 rounded-t-md bg-violet-500/50"></div>
                            <div class="h-[38%] flex-1 rounded-t-md bg-violet-500/60"></div>
                            <div class="h-[65%] flex-1 rounded-t-md bg-violet-500/70"></div>
                            <div class="h-[55%] flex-1 rounded-t-md bg-violet-400/80"></div>
                            <div class="h-[82%] flex-1 rounded-t-md bg-cyan-400"></div>
                            <div class="h-[72%] flex-1 rounded-t-md bg-cyan-300"></div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-4">
                            <div class="rounded-xl border border-white/5 bg-white/[0.03] p-4">
                                <p class="text-sm text-slate-400">
                                    Tracked items
                                </p>
                                <p class="mt-2 text-2xl font-bold">—</p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Ready for data
                                </p>
                            </div>

                            <div class="rounded-xl border border-white/5 bg-white/[0.03] p-4">
                                <p class="text-sm text-slate-400">
                                    Price history
                                </p>
                                <p class="mt-2 text-2xl font-bold text-cyan-300">
                                    <span aria-hidden="true">↗</span> Trends
                                </p>
                                <p class="mt-1 text-xs text-slate-500">
                                    Coming soon
                                </p>
                            </div>
                        </div>

                        <p class="mt-4 text-xs text-slate-500">
                            Illustrative UI only — no live market data yet.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section
                id="features"
                class="border-t border-white/10 bg-slate-900/40"
            >
                <div class="mx-auto max-w-7xl px-6 py-20 lg:px-8">
                    <div class="max-w-2xl">
                        <p
                            class="text-sm font-semibold uppercase tracking-[0.2em] text-violet-400"
                        >
                            What we're building
                        </p>
                        <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">
                            Everything you need to read the market.
                        </h2>
                        <p class="mt-4 leading-7 text-slate-400">
                            A clear view of item values and how they change
                            across leagues.
                        </p>
                    </div>

                    <div class="mt-12 grid gap-5 md:grid-cols-3">
                        <article
                            v-for="feature in features"
                            :key="feature.number"
                            class="group rounded-2xl border border-white/10 bg-slate-950/70 p-7 transition duration-300 hover:-translate-y-1 hover:border-violet-400/40"
                        >
                            <div class="flex items-center justify-between">
                                <span
                                    class="flex h-12 w-12 items-center justify-center rounded-xl bg-violet-500/10 text-2xl text-violet-300"
                                >
                                    {{ feature.icon }}
                                </span>
                                <span class="text-sm text-slate-600">
                                    {{ feature.number }}
                                </span>
                            </div>

                            <h3 class="mt-6 text-xl font-semibold">
                                {{ feature.title }}
                            </h3>

                            <p class="mt-3 leading-7 text-slate-400">
                                {{ feature.description }}
                            </p>
                        </article>
                    </div>
                </div>
            </section>

            <!-- About / CTA -->
            <section id="about" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
                <div
                    class="flex flex-col justify-between gap-6 rounded-2xl border border-violet-400/20 bg-gradient-to-r from-violet-950/60 to-slate-900 p-8 sm:flex-row sm:items-center sm:p-10"
                >
                    <div>
                        <h2 class="text-2xl font-bold">
                            Your next trade starts with insight.
                        </h2>
                        <p class="mt-2 text-slate-400">
                            The foundation for smarter market decisions.
                        </p>
                    </div>

                    <a
                        href="#features"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-slate-950 transition hover:bg-violet-200"
                    >
                        Coming soon
                        <span aria-hidden="true">→</span>
                    </a>
                </div>
            </section>
        </main>

        <footer class="border-t border-white/10">
            <div
                class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-7 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between lg:px-8"
            >
                <p>
                    © {{ new Date().getFullYear() }} TLI Market Insights
                </p>
                <p>An independent community project for Torchlight: Infinite.</p>
            </div>
        </footer>
    </div>
</template>
