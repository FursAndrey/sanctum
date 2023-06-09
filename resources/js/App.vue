<template>
    <div>
        <div v-if="token && isAdmin()" class="admin-hamburger-menu">
            <input id="admin_menu__toggle" type="checkbox" />
            <label class="menu__btn" for="admin_menu__toggle">
                <span></span>
            </label>
            <div class="menu-title">ADMIN MENU</div>

            <ul class="menu__box">
                <li class="p-2"><router-link :to="{ name: 'post.index'}" class="menu__item">Posts</router-link></li>
                <li class="p-2"><router-link :to="{ name: 'role.index'}" class="menu__item">Roles</router-link></li>
                <li class="p-2"><router-link :to="{ name: 'user.index'}" class="menu__item">Users</router-link></li>
            </ul>
        </div>

        <div class="header-hamburger-menu">
            <input id="header_menu__toggle" type="checkbox" />
            <label class="menu__btn" for="header_menu__toggle">
                <span></span>
            </label>
            <div class="menu-title">MENU</div>

            <ul class="menu__box">
                <li class="p-2"><router-link :to="{ name: 'postList'}" class="menu__item">Post list</router-link></li>
                <li class="p-2"><router-link :to="{ name: 'about'}" class="menu__item">About</router-link></li>
                <li v-if="!token" class="p-2"><router-link :to="{ name: 'login'}" class="menu__item">Login</router-link></li>
                <li v-if="!token" class="p-2"><router-link :to="{ name: 'registration'}" class="menu__item">Registration</router-link></li>
                <li v-if="token" class="p-2">
                    <span v-if="currentUserForMenu">Hi, {{ currentUserForMenu.name }}. </span>
                    <span @click.prevent="logout" class="menu__item cursor-pointer">Logout</span>
                </li>
            </ul>
        </div>
        
        <router-view class="mx-auto w-3/5"></router-view>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            token: null,
            currentUserForMenu: ''
        }
    },
    
    mounted() {
        this.getToken();
    },

    watch: {
        $route(to, from) {

            //моя проверка авторизации
            axios.get('/api/currentUser')
                .then( res => {
                    this.currentUserForMenu = res.data.data;
                    
                    if (this.currentUserForMenu.name == undefined) {
                        localStorage.key('x_xsrf_token') ? localStorage.removeItem('x_xsrf_token') : '';
                    }
                    
                    this.getToken();

                    const fullPath = to.fullPath;
                    //если не авторизованый пользователь лезет в админку - отправить на страницу логина
                    if (fullPath.indexOf('admin/') != -1) {
                        if (!this.token) {
                            this.$router.push({name: 'login'})
                        }
                    }

                    //если пользователь авторизован, но не имеет роли "админ" и лезет в админку
                    if (fullPath.indexOf('admin/') != -1) {
                        if (!this.isAdmin()) {
                            this.$router.push({name: 'errors.403'})
                        }
                    }
                });
                
        },
    },

    methods: {
        getToken() {
            this.token = localStorage.getItem('x_xsrf_token')
        },

        logout() {
            axios.post('/logout')
                .then( res => {
                    localStorage.removeItem('x_xsrf_token')
                    this.$router.push({name: 'postList'})
                })
        },

        getCurrentUserForMenu() {
            axios.get('/api/currentUser')
                .then( res => {
                    this.currentUserForMenu = res.data.data;
                });
        },

        isAdmin() {
            if (this.currentUserForMenu.roles == undefined) {
                return false;
            }
            if (this.currentUserForMenu.roles.includes("Admin")) {
                return true;
            } else {
                return false;
            }
        }
    }
}
</script>

<style scoped>
div.header-menu, 
.header-hamburger-menu {
    background-color: black;
}
div.admin-menu,
.admin-hamburger-menu {
    background-color: #211c3c;
}
.menu-title {
    margin: -4px 0 0 60px;
    color: #7270d9;
    z-index: 2;
    position: relative;
}
.admin-hamburger-menu > input, .admin-hamburger-menu > label,
.header-hamburger-menu > input, .header-hamburger-menu > label, .menu-title {
    display: none;
}
.menu__box {
    display: flex;
    justify-content: space-between;
    width: 24rem;
    margin-left: auto;
    margin-right: auto;
}
@media (max-width: 750px) {
    .admin-hamburger-menu > input, .admin-hamburger-menu > label,
    .header-hamburger-menu > input, .header-hamburger-menu > label, .menu-title {
        display: block;
    }
    .admin-hamburger-menu, .header-hamburger-menu {
        position: relative;
        height: 40px;
    }

    #admin_menu__toggle, #header_menu__toggle {
        opacity: 0;
    }
    #admin_menu__toggle:checked + .menu__btn > span,
    #header_menu__toggle:checked + .menu__btn > span {
        transform: rotate(45deg);
    }
    #admin_menu__toggle:checked + .menu__btn > span::before,
    #header_menu__toggle:checked + .menu__btn > span::before {
        top: 0;
        transform: rotate(0deg);
    }
    #admin_menu__toggle:checked + .menu__btn > span::after,
    #header_menu__toggle:checked + .menu__btn > span::after {
        top: 0;
        transform: rotate(90deg);
    }
    #admin_menu__toggle:checked ~ .menu__box,
    #header_menu__toggle:checked ~ .menu__box {
        left: 0 !important;
    }
    .menu__btn {
        position: absolute;
        top: 20px;
        left: 20px;
        width: 26px;
        height: 26px;
        cursor: pointer;
        z-index: 2;
    }
    .menu__btn > span,
    .menu__btn > span::before,
    .menu__btn > span::after {
        display: block;
        position: absolute;
        width: 100%;
        height: 2px;
        background-color: #616161;
        transition-duration: .25s;
    }
    .menu__btn > span::before {
        content: '';
        top: -8px;
    }
    .menu__btn > span::after {
        content: '';
        top: 8px;
    }
    .menu__box {
        display: block;
        position: fixed;
        top: 0;
        left: -100%;
        width: 300px;
        height: 100%;
        margin: 0;
        padding: 280px 0 0 0;
        list-style: none;
        background-color: #ECEFF1;
        box-shadow: 2px 2px 6px rgba(0, 0, 0, .4);
        transition-duration: .25s;
        z-index: 1;
    }
    .menu__item {
        display: block;
        padding: 12px 24px;
        color: #333;
        font-family: 'Roboto', sans-serif;
        font-size: 20px;
        font-weight: 600;
        text-decoration: none;
        transition-duration: .25s;
    }
    .menu__item:hover {
        background-color: #CFD8DC;
    }
}
</style>
