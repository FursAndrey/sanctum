<script setup>
import { onMounted, ref } from 'vue';
import Dropzone from 'dropzone';

const props = defineProps(['previousUploadedImages', 'imagesDelete']);
const div_dropzone = ref(null);
const obj_dropzone = ref();
const message_error = ref({visible:false, text:''});

function getFilesImages() {
    let files_images = [];
    obj_dropzone.value.getAcceptedFiles().forEach(function(image, i, arr) {
        files_images.push(image);
    });
    return files_images;
}

defineExpose({
    getFilesImages,
});

onMounted(() => {

    obj_dropzone.value = new Dropzone(
        div_dropzone.value,
        {
            url: '/',
            parallelUploads: 10,
            autoProcessQueue: false,
            addRemoveLinks: false, //ссылки на удаленеи по умолчанию
            acceptedFiles: 'image/jpeg,.png',
            previewTemplate: document.getElementById('template-preview').innerHTML,
            previewsContainer: document.getElementById('dz-container'), //предварительный просмотр
            // dictDefaultMessage: 'fdgdfg',
            // maxFiles: 1 - props.category.images.length,
            maxFilesize: 2,
            thumbnailHeight: 120,
            thumbnailWidth: null,
            dictInvalidFileType: "Вы не можете загружать файлы этого типа, разрешены только фото jpeg,jpg,png,",
            dictMaxFilesExceeded: "Вы превысили максимально возможное кол-во файлов",
            dictFileTooBig:"Файл слишком большой ({{filesize}} Mb). Максимальный размер файла: {{maxFilesize}} Mb.",
            dictFallbackMessage:"Ваш браузер не поддерживает загрузку файлов drag'n'drop",
            ignoreHiddenFiles: true,

            accept(file, done) {
                message_error.value.visible = false;
                message_error.value.text = '';
                return done();
            },

            error(file, message) {
                message_error.value.visible = true;
                message_error.value.text = message;
                obj_dropzone.value.removeFile(file);
            },

            init: function () {
            // console.log('загрузилась');
            }
        }
    );

    //добавляю ранее загруженные картинки в превью
    props.previousUploadedImages.forEach(function (image, i, arr) {
        let mockFile = { name: image.orig.name, size: image.orig.size, url: image.orig.url };
        obj_dropzone.value.displayExistingFile(mockFile, image.preview.url + image.preview.name);
    });

    //событие, которое возникает при удалении файлов
    obj_dropzone.value.on('removedfile', (file) => {
        if (file.type == undefined) {
            props.imagesDelete.push(file);
            console.log(props.imagesDelete);
        } else {
        }
    })

    obj_dropzone.value.on("addedfile", function (file) {
        console.log(obj_dropzone.value.files);
        //проверка на тип файла
        if (file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/jpeg') {
            obj_dropzone.value.removeFile(file);
            return;
        }
    });

    //функция для обработки максимального кол-во файлов
    obj_dropzone.value.on("maxfilesexceeded", function (file) {
        console.log(props.category.images.length);
        return;
        obj_dropzone.value.removeAllFiles();
        obj_dropzone.value.addFile(file);
    });
});
</script>

<template>
    <div class="border border-0 rounded flex flex-wrap">
        <div id="dz-container" class="select-none flex flex-wrap gap-x-2 gap-y-2"></div>
    </div>

    <div class="mb-2 mt-2">
        <div class="select-none relative flex flex-col text-center items-center justify-center p-2 w-full border-2 border-gray-300 border-dashed rounded-lg
            cursor-pointer bg-white hover:bg-blue-50 dark:hover:bg-zinc-700 dark:bg-zinc-800 dark:border-zinc-500 dark:hover:border-gray-400">
            <svg aria-hidden="true"
                class="select-none w-10 h-10 mb-3 text-gray-400 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="select-none z-0 mb-2 text-sm text-gray-500 dark:text-gray-300">
                <span class="font-semibold">Нажмите, чтобы загрузить</span> или перетащите
            </p>
            <p class="select-none z-0 text-xs text-gray-500 dark:text-gray-300">PNG, JPG or JEPEG</p>
            <div ref="div_dropzone" class="absolute h-36 w-full "></div>
        </div>
    </div>

    <div v-if="message_error.visible" class="bg-red-50 border border-red-200 rounded-md p-4" role="alert">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-4 w-4 text-red-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm text-red-800 font-semibold">{{ message_error.text }}</h3>
            </div>
        </div>
    </div>

    <!-- start::maket dropzone -->
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
    <!-- end::maket dropzone -->
</template>