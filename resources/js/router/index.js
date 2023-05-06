import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/index',
            component: () => import('../views/Index.vue'),
            name: 'Index',
        },
        {
            path: '/page',
            component: () => import('../views/Page.vue'),
            name: 'Page',
        }
    ],
});

export default router;