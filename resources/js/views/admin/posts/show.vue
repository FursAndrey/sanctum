<template>
    <div v-if="post" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ post.title }}</h1>
        <div class="text-sm text-right text-slate-400">{{ post.published }}</div>
        <div><img class="mb-3 mx-auto w-100" v-if="post.preview" :src="post.preview.url_original" :alt="post.title"/></div>
        <comment-count-template>{{ post.commentCount }}</comment-count-template>
        <div>{{ post.body }}</div>
        <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to posts</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../../../composition/posts';
import commentCountTemplate from '../../../components/commentCountTemplate.vue';
export default {
  components: { commentCountTemplate },
    name: "PostShow",

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
