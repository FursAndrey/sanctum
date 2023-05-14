<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Posts</h1>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-4 lg:-mx-6">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <router-link :to="{ name: 'post.create'}" class="block mx-auto mb-4 w-48 p-2 font-bold bg-lime-600 text-white rounded-lg text-center">Create new post</router-link>
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center text-sm font-light">
                            <thead class="border-b bg-neutral-800 font-medium text-white dark:border-neutral-500 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class=" px-6 py-4">Title</th>
                                    <th scope="col" class=" px-6 py-4">Published</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="post in posts" :key="post.id" class="border-b dark:border-neutral-500">
                                    <td class="whitespace-nowrap px-6 py-4 text-left">
                                        <router-link :to="{ name: 'post.show', params:{ id: post.id } }" class="p-2 me-6 font-bold bg-sky-700 text-white rounded-lg text-center">learn more</router-link>
                                        {{ post.title }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ post.published }}</td>
                                    <td>
                                        <router-link :to="{ name: 'post.edit', params:{ id: post.id } }" class="p-2 me-6 font-bold bg-amber-600 text-white rounded-lg text-center">Edit</router-link>
                                        <span @click="deletePost(post.id)" class="p-2 me-6 font-bold bg-red-700 text-white rounded-lg text-center cursor-pointer">Delete</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PostIndex',

    data() {
        return {
            posts: null,
            errors: [],
        }
    },

    mounted() {
        this.getPosts()
    },

    methods: {
        getPosts() {
            axios.get('/api/posts').then(res => {
                this.posts = res.data.data;
            }).catch(error=>{
                if (error.response.status == 401) {
                    // console.log(error);
                } else {
                    // console.log(error);
                }
            })
        },

        deletePost(id) {
            console.log(id);
            if (!confirm('Are you sure?')) {
                return false;
            }
            axios.delete('/api/posts/'+id)
                .then((response) => {
                    if (response.status == 204) {
                        this.posts = this.posts.filter(p => p.id !== id);
                    }
                })
                .catch((error) => {
                    // console.log('error remove post');
                    // console.log(error);
                });
        }
    }
}
</script>

<style scoped>

</style>