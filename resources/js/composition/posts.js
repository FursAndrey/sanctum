import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function usePosts() {
    const image = ref([]);
    const post = ref([]);
    const posts = ref([]);
    const meta = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getPost = async (id) => {
        let response = await axios.get('/api/posts/' + id);
        post.value = response.data.data;
        image.value = response.data.data.preview;
    }

    const getPosts = async (page) => {
        let response = await axios.get('/api/posts', { params: {'page': page} });
        posts.value = response.data.data.posts;
        meta.value = response.data.data.meta;
    }
    
    const storePost = async (data) => {
        errorMessage.value = '';
        data.image_id = image.value.id? image.value.id: null;

        try {
            await axios.post('/api/posts', data);
            await router.push({ name: 'post.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }

    const updatePost = async (id) => {
        errorMessage.value = '';
        const image_id = image.value.id? image.value.id: null;
        
        try {
            await axios.put('/api/posts/' + id, {title: post.value.title, body: post.value.body, image_id: image_id});
            await router.push({ name: 'post.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }
    
    const destroyPost = async (id) => {
        await axios.delete('/api/posts/'+id);
    }
    
    const cancelSelectPreview = () => {
        image.value = null;
        post.image_id = null;
    }

    const saveImage = (e) => {
        let file = e.target.files[0];
        const formData = new FormData();
        formData.append('image', file);
        
        errorMessage.value = '';
        axios.post(
            '/api/preview', 
            formData, 
            {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(res => {
                image.value = res.data.data;
            }).catch(e => {
                errorMessage.value = e.response.data.message;
            });
    }

    const storeRandomPost = async () => {
        await axios.post('/api/posts/storeRandomPost')
            .then(res => {
                getPosts();
            // }).catch(e => {
            //     console.log(e);
            });
    }

    return {
        post,
        posts,
        meta,
        image,
        errorMessage,
        getPost,
        getPosts,
        storeRandomPost,
        storePost,
        updatePost,
        destroyPost,
        cancelSelectPreview,
        saveImage
    }
}