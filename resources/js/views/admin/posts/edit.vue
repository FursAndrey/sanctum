<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new post</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="post.title" type="text" placeholder="title" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <textarea v-model="post.body" rows="4" class="block p-3 mb-2 w-full rounded-lg border border-gray-300" placeholder="Write body your post here..."></textarea>
        </div>
        
        <div class="flex space-x-8">
            <div>
                <input @change="storeImage" ref="file" type="file" class="hidden">
                <span class="block w-48 p-2 mb-2 bg-teal-700 text-white rounded-lg text-center cursor-pointer" @click.prevent="selectFile()">Preview</span>
            </div>
            <div v-if="image && image.url" @click="cancelSelectPreview()" class="block w-44 p-2 mb-2 ml-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 text-center cursor-pointer">
                Cancel
            </div>
        </div>
        <div v-if="image && image.url" class="mb-2">
            <img :src="image && image.url"/>
        </div>

        <div class="flex justify-between">
            <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to posts</router-link>
            <input @click.prevent="editPost" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import { onMounted } from 'vue';
import usePosts from '../../../composition/posts';
export default {
    name: "PostEdit",
    
    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { image, post, errorMessage, getPost, updatePost, cancelSelectPreview, saveImage } = usePosts();

        const editPost = async () => {
            await updatePost(props.id);
        }

        const storeImage = (e) => {
            saveImage(post, e);
        }

        const getCurrentPost = () => {
            getPost(props.id);
        }
        
        onMounted(getCurrentPost);

        return {
            post,
            image,
            errorMessage,
            getPost,
            editPost,
            storeImage,
            cancelSelectPreview
        }
    },

    methods: {
        selectFile() {
            this.fileInput = this.$refs.file;
            this.fileInput.click();
        },
    }
}
</script>

<style scoped>

</style>
