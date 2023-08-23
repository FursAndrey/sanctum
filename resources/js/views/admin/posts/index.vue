<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Posts</h1>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-4 lg:-mx-6">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="flex mx-auto mb-4 w-4/5">
                        <!-- <router-link :to="{ name: 'post.create'}" class="block p-2 w-44 me-4 font-bold bg-lime-600 text-white rounded-lg text-center">Create new post</router-link> -->
                        <router-link :to="{ name: 'post.create2'}" class="block p-2 w-44 me-4 font-bold bg-lime-600 text-white rounded-lg text-center">Create new post</router-link>
                        <span @click="storeRandomPost()" class="block p-2 w-44 me-4 font-bold bg-amber-700 text-white rounded-lg text-center cursor-pointer">Create random post</span>
                        <span @click="storeRandomComment()" class="block p-2 w-56 font-bold bg-amber-700 text-white rounded-lg text-center cursor-pointer">Create random comment</span>
                    </div>
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center text-sm font-light">
                            <thead class="border-b bg-neutral-800 font-medium text-white dark:border-neutral-500 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Preview</th>
                                    <th scope="col" class="px-6 py-4">Title</th>
                                    <th scope="col" class="px-6 py-4">Published</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="post in posts" :key="post.id" class="border-b dark:border-neutral-500">
                                    <td class="whitespace-nowrap px-6 py-4"><img class="mx-auto w-28" v-if="post.preview" :src="post.preview.url_preview" :alt="post.title"/></td>
                                    <td class="px-6 py-4 text-left">
                                        <router-link :to="{ name: 'post.show', params:{ id: post.id } }" class="p-2 me-6 font-bold bg-sky-700 text-white rounded-lg text-center">learn more</router-link>
                                        <span class="leading-8">{{ post.title }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ post.published }}</td>
                                    <td>
                                        <!-- <router-link :to="{ name: 'post.edit', params:{ id: post.id } }" class="p-2 me-6 font-bold bg-amber-600 text-white rounded-lg text-center">Edit</router-link> -->
                                        <router-link :to="{ name: 'post.edit2', params:{ id: post.id } }" class="p-2 me-6 font-bold bg-amber-600 text-white rounded-lg text-center">Edit</router-link>
                                        <span @click="deletePost(post.id)" class="p-2 me-6 font-bold bg-red-700 text-white rounded-lg text-center cursor-pointer">Delete</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <pagination-template @changePage="changePages" v-bind:meta="meta"></pagination-template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import usePosts from '../../../composition/posts';
import useComments from '../../../composition/comments';
import paginationTemplate from '../../../components/paginationTemplate.vue';
export default {
    components: { 
        paginationTemplate 
    },
    name: 'PostIndex',

    setup() {
        const { posts, meta, getPosts, destroyPost, storeRandomPost } = usePosts();
        const { storeRandomComment } = useComments();
        const firstPage = 1;

        onMounted(getPosts(firstPage));

        const deletePost = async (id) => {
            if (!window.confirm('Are you sure?')) {
                return false;
            }

            await destroyPost(id);
            await getPosts(firstPage);
        }

        const changePages = async (page) => {
            await getPosts(page);
        }

        return {
            posts,
            meta,
            deletePost,
            storeRandomPost,
            storeRandomComment,
            changePages,
        }
    },
}
</script>

<style scoped>

</style>