<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Home page: Post list</h1>
        <div class="flex justify-between flex-wrap">
            <div
                v-for="post in posts" 
                :key="post.id" 
                class="flex flex-col mx-auto w-64 m-4 p-4 text-justify border border-indigo-600 shadow-md shadow-indigo-800">
                <img class="mx-auto w-64 mb-3" v-if="post.preview" :src="post.preview.url_preview" :alt="post.title"/>
                <div class="whitespace-nowrap pb-4 text-right text-sm text-gray-500">{{ post.published }}</div>
                <div class="flex">
                    <comment-count-template>{{ post.commentCount }}</comment-count-template>
                    <post-like-template v-bind:is_liked="post.is_liked" v-bind:like_count="post.likeCount" v-bind:post_id="String(post.id)"></post-like-template>
                </div>
                <div class="flex-1 mb-4">{{ post.title }}</div>
                <div>
                    <router-link 
                        :to="{ name: 'postPage', params:{ id: post.id } }"
                        class="w-full block p-3 text-center rounded-lg bg-indigo-800">
                        More info ...
                    </router-link>
                </div>
            </div>
        </div>
        <pagination-template @changePage="changePages" v-bind:meta="meta"></pagination-template>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../composition/posts';
import paginationTemplate from '../components/paginationTemplate.vue';
import CommentCountTemplate from '../components/commentCountTemplate.vue';
import PostLikeTemplate from '../components/postLikeTemplate.vue';
export default {
    components: { 
        paginationTemplate,
        CommentCountTemplate,
        PostLikeTemplate
    },
    name: 'postList',
    
    setup() {
        const { posts, meta, getPosts } = usePosts();
        const firstPage = 1;

        onMounted(getPosts(firstPage));

        const changePages = async (page) => {
            await getPosts(page);
        }

        return {
            posts,
            meta,
            changePages
        }
    },
}
</script>

<style scoped>

</style>
