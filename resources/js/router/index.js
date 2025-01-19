import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    // history: createWebHistory(import.meta.env.BASE_URL),
    history: createWebHistory(),
    routes: [
        {
            path: '/calculator',
            component: () => import('../views/Calculator.vue'),
            name: 'calculator',
        },
        {
            path: '/admin/breakers',
            component: () => import('../views/admin/breakers/index.vue'),
            name: 'breaker.index',
        },
        {
            path: '/admin/breakers/create',
            component: () => import('../views/admin/breakers/create.vue'),
            name: 'breaker.create',
        },
        {
            path: '/',
            component: () => import('../views/postList.vue'),
            name: 'postList',
        },
        {
            path: '/post/:id',
            component: () => import('../views/postPage.vue'),
            name: 'postPage',
            props: true
        },
        {
            path: '/about',
            component: () => import('../views/about.vue'),
            name: 'about',
        },
        {
            path: '/chats',
            component: () => import('../views/chat/index.vue'),
            name: 'chats',
        },
        {
            path: '/chats/:id',
            component: () => import('../views/chat/show.vue'),
            name: 'chats.show',
            props: true
        },
        {
            path: '/login',
            component: () => import('../views/login.vue'),
            name: 'login',
        },
        {
            path: '/registration',
            component: () => import('../views/registration.vue'),
            name: 'registration',
        },
        {
            path: '/admin/posts',
            component: () => import('../views/admin/posts/index.vue'),
            name: 'post.index',
        },
        {
            path: '/admin/posts/create',
            component: () => import('../views/admin/posts/create.vue'),
            name: 'post.create',
        },
        {
            path: '/admin/posts/create2',
            component: () => import('../views/admin/posts/create2.vue'),
            name: 'post.create2',
        },
        {
            path: '/admin/posts/show/:id',
            component: () => import('../views/admin/posts/show.vue'),
            name: 'post.show',
            props: true
        },
        {
            path: '/admin/posts/edit/:id',
            component: () => import('../views/admin/posts/edit.vue'),
            name: 'post.edit',
            props: true
        },
        {
            path: '/admin/posts/edit2/:id',
            component: () => import('../views/admin/posts/edit2.vue'),
            name: 'post.edit2',
            props: true
        },
        {
            path: '/admin/holydays',
            component: () => import('../views/admin/timeCalculator/holydays/index.vue'),
            name: 'holyday.index',
        },
        {
            path: '/admin/holydays/create',
            component: () => import('../views/admin/timeCalculator/holydays/create.vue'),
            name: 'holyday.create',
        },
        {
            path: '/admin/roles',
            component: () => import('../views/admin/roles/index.vue'),
            name: 'role.index',
        },
        {
            path: '/admin/roles/create',
            component: () => import('../views/admin/roles/create.vue'),
            name: 'role.create',
        },
        {
            path: '/admin/roles/show/:id',
            component: () => import('../views/admin/roles/show.vue'),
            name: 'role.show',
            props: true
        },
        {
            path: '/admin/roles/edit/:id',
            component: () => import('../views/admin/roles/edit.vue'),
            name: 'role.edit',
            props: true
        },
        {
            path: '/admin/users',
            component: () => import('../views/admin/users/index.vue'),
            name: 'user.index',
        },
        {
            path: '/admin/users/show/:id',
            component: () => import('../views/admin/users/show.vue'),
            name: 'user.show',
            props: true
        },
        {
            path: '/admin/users/edit/:id',
            component: () => import('../views/admin/users/edit.vue'),
            name: 'user.edit',
            props: true
        },
        {
            path: '/profile/show/:id',
            component: () => import('../views/profile/show.vue'),
            name: 'profile',
            props: true
        },
        {
            path: '/profile/edit/:id',
            component: () => import('../views/profile/edit.vue'),
            name: 'profile.edit',
            props: true
        },
        {
            path: '/403',
            component: () => import('../views/errors/403.vue'),
            name: 'errors.403'
        },
        {
            path: '/admin/caheClear',
            component: () => import('../views/admin/cacheClear.vue'),
            name: 'cahe.clear'
        },
    ],
});

router.beforeEach((to, from, next) => {
    // axios.get('/api/user')
    //     .then(res => {
    //     })
    //     .catch(e => {
    //         if (e.response.status === 401) {
    //             localStorage.key('x_xsrf_token') ? localStorage.removeItem('x_xsrf_token') : '';
    //         }
    //     })

    // //моя проверка авторизации
    // axios.get('/api/close')
    //     .then(res => {
    //     })
    //     .catch(err => {
    //         if (err.response.status === 401) {
    //             localStorage.key('x_xsrf_token') ? localStorage.removeItem('x_xsrf_token') : '';
    //         }
    //     })
    // const token = localStorage.getItem('x_xsrf_token')
    
    // const fullPath = to.fullPath;
    // //если не авторизованый пользователь лезет в админку - отправить на страницу логина
    // if (fullPath.indexOf('admin/') != -1) {
    //     if (!token) {
    //         return next({name: 'login'});
    //     } else {
    //         return next();
    //     }
    // }

    next()
})

export default router;