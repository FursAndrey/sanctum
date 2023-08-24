<template>
    <div v-if="post" class="mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ post.title }}</h1>
        <div class="text-sm text-right text-slate-400">{{ post.published }}</div>
        <div><img class="mb-3 mx-auto w-100" v-if="post.preview" :src="post.preview.url_original" :alt="post.title"/></div>
        <div>{{ post.body }}</div>
        <router-link :to="{ name: 'postList'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to post list</router-link>
        <div>
            <h2 class="text-2xl font-bold text-center">Comments</h2>
            <div v-if="isAuth()" class="mx-auto w-3/5">
                <textarea v-model="comment" placeholder="body of comment" class="w-full h-32 p-3 border-2 rounded-lg border-sky-500"></textarea>
                <input @click="createComment()" type="button" value="Create comment" class="block w-48 p-3 mb-2 rounded-lg bg-sky-500 text-white hover:bg-sky-700 font-semibold"/>
            </div>
            <div v-for="comment in comments" :key="comment.id" class="border-t border-sky-500 mt-2 pt-2">
                <div class="text-sm text-left text-slate-400">{{ comment.user }}</div>
                <div class="text-sm text-right text-slate-400">{{ comment.published }}</div>
                <div>{{ comment.body }}</div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../composition/posts';
import useComments from '../composition/comments';
import useInspector from '../composition/inspector';
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
        const { comment, comments, getComments, storeComment } = useComments();
        const { isAuth } = useInspector();
        
        const getCurrentPost = () => {
            getPost(props.id);
        }
        
        const createComment = () => {
            storeComment(props.id, comment);
        }
        
        onMounted(getCurrentPost);
        onMounted(getComments(props.id));

        return {
            post,
            comment,
            comments,
            createComment,
            isAuth
        }
    }
}
</script>

<style scoped>

</style>
