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
                <span class="block w-48 p-2 mb-2 bg-teal-700 text-white rounded-lg text-center cursor-pointer" @click.prevent="selectFile()">Image</span>
            </div>
            <div v-if="image" @click="image = null" class="block w-44 p-2 mb-2 ml-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700 text-center">
                Cancel
            </div>
        </div>
        <div v-if="image" class="mb-2">
            <img :src="image.url"/>
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'post.index'}" class="block w-48 p-2 bg-amber-600 text-white rounded-lg text-center">Return to posts</router-link>
            <input @click.prevent="store" type="submit" value="Store" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
export default {
    name: "PostCreate",
    
    data() {
        return {
            errorMessage: null,
            title: null,
            body: null,
            image: null,
        }
    },

    methods: {
        store() {
            axios.post('/api/posts', {title: this.title, body: this.body})
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
                    this.image = res.data.data;
                }).catch(error=>{
                    this.errorMessage = error.response.data.message;
                })
        },
    }
}
</script>

<style scoped>

</style>
