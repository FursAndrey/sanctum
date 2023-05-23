<template>
    <div v-if="post" class="mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ post.title }}</h1>
        <div class="text-sm text-right text-slate-400">{{ post.published }}</div>
        <div><img class="mb-3 mx-auto w-80" v-if="post.preview" :src="post.preview.url" :alt="post.title"/></div>
        <div>{{ post.body }}</div>
        <router-link :to="{ name: 'postList'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to post list</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../composition/posts';
export default {
    name: "postPage",

    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { post, getPost } = usePosts();
        
        const getCurrentPost = () => {
            getPost(props.id);
        }
        
        onMounted(getCurrentPost);

        return {
            post
        }
    }
}
</script>

<style scoped>

</style>
