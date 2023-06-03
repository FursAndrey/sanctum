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
        
        <div v-if="meta" class="pt-4 pb-6">Posts form: {{ meta.from }} to: {{ meta.to }}. Total posts: {{ meta.total }}.</div>
        <div v-if="meta" class="pb-2 mb-8">
            <span @click="firstPage()" class="w-16 p-2 me-4 bg-amber-600 rounded-lg text-center cursor-pointer">first</span>
            <span @click="previusPage()" class="w-16 p-2 me-4 bg-amber-600 rounded-lg text-center cursor-pointer">previus</span>
            <span @click="nextPage()" class="w-16 p-2 me-4 bg-amber-600 rounded-lg text-center cursor-pointer">next</span>
            <span @click="lastPage()" class="w-16 p-2 bg-amber-600 rounded-lg text-center cursor-pointer">last</span>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../composition/posts';
import usePagination from '../composition/pagination';
export default {
    name: 'postList',
    
    setup() {
        const { posts, meta, getPosts } = usePosts();
        const { page, goToFirstPage, goToNextPage, goToPreviusPage, goToLastPage } = usePagination();

        onMounted(getPosts(page.value));

        const firstPage = async () => {
            goToFirstPage();
            await getPosts(page.value);
        }

        const lastPage = async () => {
            goToLastPage(meta.value.last_page);
            await getPosts(page.value);
        }

        const nextPage = async () => {
            goToNextPage(meta.value.last_page);
            await getPosts(page.value);
        }

        const previusPage = async () => {
            goToPreviusPage();
            await getPosts(page.value);
        }

        return {
            posts,
            meta,
            firstPage,
            lastPage,
            nextPage,
            previusPage
        }
    },
}
</script>

<style scoped>

</style>
