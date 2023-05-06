<template>
    <div>
        App
        <h1 class="text-3xl font-bold underline">
            Hello world!
        </h1>
        <div class="flex justify-between p-8 w-96 mx-auto">

            <router-link :to="{ name: 'Index'}" class="p-2">Index</router-link>
            <router-link :to="{ name: 'Page'}" class="p-2">Page</router-link>
            <!-- <router-link :to="{ name: 'get.index'}" class="p-2">Get</router-link>
            <router-link :to="{ name: 'login'}" class="p-2">Login</router-link>
            <router-link :to="{ name: 'registration'}" class="p-2">Registration</router-link> -->
        </div>
        
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

</style>
