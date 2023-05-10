<template>
    <div>
        <div class="header-menu">
            <div class="flex justify-between w-96 mx-auto">
                <router-link :to="{ name: 'Index'}" class="p-2">Index</router-link>
                <router-link :to="{ name: 'Page'}" class="p-2">Page</router-link>
                <router-link :to="{ name: 'login'}" class="p-2">Login</router-link>
                <router-link :to="{ name: 'registration'}" class="p-2">Registration</router-link>
            </div>
        </div>
        <h1 class="text-3xl font-bold underline">
            Hello world!
        </h1>
        
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        
    },
}
</script>

<style scoped>
div.header-menu {
    background-color: black;
}
</style>
