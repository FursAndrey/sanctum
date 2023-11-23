<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Edit this post</h1>
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
            <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to posts</router-link>
            <input @click.prevent="editPost" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import { ref } from "vue";
import { useRouter } from "vue-router";
import Dropzone from "dropzone";
export default {
    name: "PostEdit2",
    
    props: {
        id: {
            required: true,
            type: String
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

        this.getCurrentPost(this.dropzone);

        this.dropzone.on('removedfile', (file) => {
            this.post.deleted_preview.push(file.id);
        })
    },

    setup(props) {
        let post = reactive({
            "title": "",
            "body": "",
            "deleted_preview": [],
        });
        const router = useRouter();
        const errorMessage = ref('');

        const editPostWithImg = async (dropzone) => {
            const files = dropzone.getAcceptedFiles();
            const formData = new FormData();
            files.forEach(file => {
                formData.append('imgs[]', file);
                dropzone.removeFile(file);
            });
            post.deleted_preview.forEach( id => {
                if (id !== undefined) {
                    formData.append('deleted_preview[]', id);
                }
            })
            formData.append('title', post.title);
            formData.append('body', post.body);
            formData.append('_method', 'PATCH');
            
            axios.post('/api/posts2/'+props.id, formData).then(res => {
                router.push({ name: 'post.index' });
            }).catch(e => {
                errorMessage.value = e.response.data.message;
            });
        }

        const getCurrentPost = async (dropzone) => {
            document.getElementById('dz-container').innerHTML = '';
            //костыль, т.к. не знаю как иначе
            await axios.get('/api/posts/' + props.id).then(res => {
                let p = res.data.data;
                
                post.title = p.title;
                post.body = p.body;
                
                let mockFile = { id: p.preview.id }; //для создания файла
                dropzone.displayExistingFile(mockFile, p.preview.url_preview);
            // }).catch(e => {
                // errorMessage.value = e.response.data.message;
            });
        }
        
        return {
            post,
            errorMessage,
            getCurrentPost,
            editPostWithImg,
        }
    },

    methods: {
        editPost() {
            this.editPostWithImg(this.dropzone);
        },
    }
}
</script>

<style scoped>

</style>
