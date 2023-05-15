<template>
    <div>
        <div v-if="token" class="admin-menu">
            <div class="flex justify-between w-96 mx-auto">
                <router-link :to="{ name: 'post.index'}" class="p-2">Posts</router-link>
            </div>
        </div>
        <div class="header-menu">
            <div class="flex justify-between w-96 mx-auto">
                <router-link :to="{ name: 'Index'}" class="p-2">Index</router-link>
                <router-link :to="{ name: 'Page'}" class="p-2">Page</router-link>
                <router-link v-if="!token" :to="{ name: 'login'}" class="p-2">Login</router-link>
                <router-link v-if="!token" :to="{ name: 'registration'}" class="p-2">Registration</router-link>
                <span v-if="token" @click.prevent="logout" class="p-2 cursor-pointer">Logout</span>
            </div>
        </div>
        
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            token: null
        }
    },
    
    mounted() {
        this.getToken()
    },

    watch: {
        $route(to, from) {
            this.getToken()
        }
    },

    methods: {
        getToken() {
            this.token = localStorage.getItem('x_xsrf_token')
        },

        logout() {
            axios.post('/logout')
                .then( res => {
                    localStorage.removeItem('x_xsrf_token')
                    this.$router.push({name: 'Index'})
                })
        }
    }
}
</script>

<style scoped>
div.header-menu {
    background-color: black;
}
div.admin-menu {
    background-color: #211c3c;
}
</style>
