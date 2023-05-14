<template>
    <div v-if="post" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ post.title }}</h1>
        <div class="text-sm text-right text-slate-400">{{ post.published }}</div>
        <div>{{ post.body }}</div>
        <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to posts</router-link>
    </div>
</template>

<script>
export default {
    name: "PostShow",

    data() {
        return {
            postId: this.$route.params.id,
            post: null,
        }
    },

    async mounted() {
        let result = await axios.get('/api/posts/'+this.postId);
        this.post = result.data.data;
    },
}
</script>

<style scoped>

</style>
