<template>
    <div ref="myDropzone" class="p-5 bg-slate-500 mb-3 rounded-lg border-gray-300 text-center cursor-pointer">Dropzone</div>
    <div @click="test">***</div>
</template>

<script>
import Dropzone from "dropzone";
export default {
    name: 'myDropzone',

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
                parallelUploads: 5,
                autoProcessQueue: false,
                acceptedFiles: 'image/jpeg,.png',
                maxFiles: 5,
                dictInvalidFileType: "Вы не можете загружать файлы этого типа, разрешены только фото jpeg,jpg,png,",
                dictMaxFilesExceeded: "Вы превысили максимально возможное кол-во файлов",
            }
        );
    },
    methods: {
        test() {
            console.log(this.dropzone.getAcceptedFiles());
        }
    }
}
</script>

<style></style>