<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new post</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="title" type="text" placeholder="title" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <textarea v-model="body" rows="4" class="block p-3 mb-2 w-full rounded-lg border border-gray-300" placeholder="Write body your post here..."></textarea>
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to posts</router-link>
            <input @click.prevent="update" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
export default {
    name: "PostEdit",
    
    data() {
        return {
            errorMessage: null,
            title: null,
            body: null,
            postId: this.$route.params.id,
        }
    },

    async mounted() {
        let result = await axios.get('/api/posts/'+this.postId);
        this.title = result.data.data.title;
        this.body = result.data.data.body;
    },

    methods: {
        update() {
            axios.patch('/api/posts/'+this.postId, {title: this.title, body: this.body})
                .then(r => {
                    // console.log(r);
                    this.$router.push({name: 'post.index'})
                })
                .catch(err => {
                    this.errorMessage = err.response.data.message;
                })
        },
    }
}
</script>

<style scoped>

</style>
