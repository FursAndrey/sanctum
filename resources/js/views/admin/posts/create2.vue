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
            <textarea v-model="post.body" rows="14" class="block p-3 mb-2 w-full rounded-lg border border-gray-300" placeholder="Write body your post here..."></textarea>
        </div>

        <div class="border border-0 rounded flex flex-wrap">
            <div id="dz-container" class="select-none flex flex-wrap gap-x-2 gap-y-2"></div>
        </div>
        <div class="hidden preview" id="template-preview">
            <div class="relative dz-preview dz-file-preview well" id="dz-preview-template">
                <div class="dz-details hidden ">
                    <div class="dz-filename">
                        <span data-dz-name></span>
                    </div>
                    <div class="dz-size" data-dz-size></div>
                </div>
                <img data-dz-thumbnail class="dz-thumbnail rounded-md border dark:border-zinc-600" />
                <div class="dz-progress">
                    <span class="dz-upload" data-dz-uploadprogress></span>
                </div>
                <div class="dz-success-mark">
                    <span></span>
                </div>
                <div class="dz-error-mark">
                    <span></span>
                </div>
                <div class="dz-error-message">
                    <span data-dz-errormessage></span>
                </div>
                <a href="#" data-dz-remove
                    class="dz-remove absolute top-0 right-0 py-0 px-1 inline-flex justify-center items-center gap-2 rounded-md border border-orange-600 font-semibold text-white
                        bg-orange-600 hover:bg-orange-700 hover:border-orange-800 focus:outline-none focus:bg-orange-700 transition-all text-sm dark:border-gray-700 dark:hover:border-orange-500">
                    X
                </a>
            </div>
        </div>
        <div ref="myDropzone" class="p-5 bg-slate-500 my-3 rounded-lg border-gray-300 text-center cursor-pointer">Dropzone</div>
        
        <div class="flex justify-between">
            <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 bg-amber-600 text-white rounded-lg text-center">Return to posts</router-link>
            <input @click.prevent="createPost" type="submit" value="Store" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import { ref } from "vue";

import { useRouter } from "vue-router";
import Dropzone from "dropzone";

export default {
    name: "PostCreate2",
    
    data() {
        return {
            dropzone: null
        }
    },

    mounted() {
        this.dropzone = new Dropzone(
            this.$refs.myDropzone, 
            {
                url: "/url/autoSend",
                parallelUploads: 3,
                autoProcessQueue: false,
                acceptedFiles: 'image/jpeg,.png',
                maxFiles: 5,
                dictInvalidFileType: "Вы не можете загружать файлы этого типа, разрешены только фото jpeg,jpg,png",
                dictMaxFilesExceeded: "Вы превысили максимально возможное кол-во файлов",
                previewTemplate: document.getElementById('template-preview').innerHTML,
                previewsContainer: document.getElementById('dz-container'),
            }
        );
    },

    methods: {
        createPost() {
            this.createPostWithImg(this.dropzone);
        },
    },

    setup() {
        let post = reactive({
            "title": "",
            "body": "",
        });
        const router = useRouter();
        const errorMessage = ref('');

        const createPostWithImg = async (dropzone) => {
            const files = dropzone.getAcceptedFiles();
            const formData = new FormData();
            files.forEach(file => {
                formData.append('imgs[]', file);
                dropzone.removeFile(file);
            });
            formData.append('title', post.title);
            formData.append('body', post.body);
            axios.post('/api/posts2', formData).then(res => {
                router.push({ name: 'post.index' });
            }).catch(e => {
                errorMessage.value = e.response.data.message;
            });
        };
        return {
            post,
            errorMessage,
            createPostWithImg,
        };
    },
}
</script>

<style scoped>

</style>
