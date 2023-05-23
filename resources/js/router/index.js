import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
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
            path: '/page',
            component: () => import('../views/Page.vue'),
            name: 'Page',
        },
        {
            path: '/login/login',
            component: () => import('../views/login.vue'),
            name: 'login',
        },
        {
            path: '/login/registration',
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

    //моя проверка авторизации
    axios.get('/api/close')
        .then(res => {
        })
        .catch(err => {
            if (err.response.status === 401) {
                localStorage.key('x_xsrf_token') ? localStorage.removeItem('x_xsrf_token') : '';
            }
        })
    const token = localStorage.getItem('x_xsrf_token')
    
    const fullPath = to.fullPath;
    //если не авторизованый пользователь лезет в админку - отправить на страницу логина
    if (fullPath.indexOf('admin/') != -1) {
        if (!token) {
            return next({name: 'login'});
        } else {
            return next();
        }
    }

    next()
})

export default router;