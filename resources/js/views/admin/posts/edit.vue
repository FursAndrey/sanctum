<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new post</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="title" type="text" placeholder="title" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <textarea v-model="body" rows="4" class="block p-3 mb-2 w-full rounded-lg border border-gray-300" placeholder="Write body your post here..."></textarea>
        </div>
        
        <div class="flex space-x-8">
            <div>
                <input @change="storeImage" ref="file" type="file" class="hidden">
                <span class="block w-48 p-2 mb-2 bg-teal-700 text-white rounded-lg text-center cursor-pointer" @click.prevent="selectFile()">Preview</span>
            </div>
            <div v-if="preview" @click="preview = null" class="block w-44 p-2 mb-2 ml-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 text-center">
                Cancel
            </div>
        </div>
        <div v-if="preview" class="mb-2">
            <img :src="preview.url"/>
        </div>

        <div class="flex justify-between">
            <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to posts</router-link>
            <input @click.prevent="update" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
export default {
    name: "PostEdit",
    
    data() {
        return {
            errorMessage: null,
            title: null,
            body: null,
            preview: null,
            postId: this.$route.params.id,
        }
    },

    async mounted() {
        let result = await axios.get('/api/posts/'+this.postId);
        this.title = result.data.data.title;
        this.body = result.data.data.body;
        this.preview = result.data.data.preview;
        console.log(result.data.data);
    },

    methods: {
        update() {
            const preview_id = this.preview? this.preview.id: null;

            axios.patch('/api/posts/'+this.postId, {title: this.title, body: this.body, image_id: preview_id})
                .then(r => {
                    // console.log(r);
                    this.$router.push({name: 'post.index'})
                })
                .catch(err => {
                    this.errorMessage = err.response.data.message;
                })
        },
        
        selectFile() {
            this.fileInput = this.$refs.file;
            this.fileInput.click();
        },
        
        storeImage(e) {
            let file = e.target.files[0];
            const formData = new FormData();
            formData.append('image', file);
            
            axios.post(
                '/api/preview', 
                formData, 
                {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(res => {
                    this.preview = res.data.data;
                }).catch(error=>{
                    this.errorMessage = error.response.data.message;
                })
        },
    }
}
</script>

<style scoped>

</style>
