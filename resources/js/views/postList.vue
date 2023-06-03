<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Home page: Post list</h1>
        <div class="flex justify-between flex-wrap">
            <div class="mx-auto w-64 m-4 p-4 text-justify border border-indigo-600" v-for="post in posts" :key="post.id">
                <img class="mx-auto w-64 mb-3" v-if="post.preview" :src="post.preview.url" :alt="post.title"/>
                <div class="whitespace-nowrap pb-4 text-right text-sm text-gray-500">{{ post.published }}</div>
                <router-link :to="{ name: 'postPage', params:{ id: post.id } }" >{{ post.title }}</router-link>
            </div>
        </div>
        <pagination-template @changePage="changePages" v-bind:meta="meta"></pagination-template>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../composition/posts';
import paginationTemplate from '../components/paginationTemplate.vue';
export default {
    components: { 
        paginationTemplate 
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
