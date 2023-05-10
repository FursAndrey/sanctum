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
        },
        {
            path: '/login/login',
            component: () => import('../views/Login.vue'),
            name: 'login',
        },
        {
            path: '/login/registration',
            component: () => import('../views/Registration.vue'),
            name: 'registration',
        }
    ],
});

router.beforeEach((to, from, next) => {
    axios.get('/api/user')
        .then(res => {
        })
        .catch(e => {
            if (e.response.status === 401) {
                localStorage.key('x_xsrf_token') ? localStorage.removeItem('x_xsrf_token') : '';
            }
        })
    const token = localStorage.getItem('x_xsrf_token')
    
    if (!token) {
        if (to.name === 'login' || to.name === 'registration') {
            return next()
        } else {
            return next({
                name: 'login'
            })
        }
    }

    if (to.name === 'login' || to.name === 'registration' && token) {
        return next({
            name: 'Index'
        })
    }

    next()

})

export default router;