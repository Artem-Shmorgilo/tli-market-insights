import { createApp, h, type DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';

const pages = import.meta.glob<{ default: DefineComponent }>(
    './pages/**/*.vue',
);

createInertiaApp({
    title: (title) =>
        title
            ? `${title} - TLI Market Insights`
            : 'TLI Market Insights',

    resolve: async (name): Promise<DefineComponent> => {
        const path = `./pages/${name}.vue`;
        const loadPage = pages[path];

        if (!loadPage) {
            throw new Error(`Page not found: ${name}`);
        }

        const module = await loadPage();

        return module.default as DefineComponent;
    },

    setup({ el, App, props, plugin }) {
        createApp({
            render: () => h(App, props),
        })
            .use(plugin)
            .mount(el);
    },
});
