import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useComments() {
    const comment = ref([]);
    const comments = ref([]);
    const errorMessage = ref('');
    
    const getComments = async (post) => {
        let response = await axios.get('/api/comments/'+post);
        
        comments.value = response.data.data;
    }

    const storeComment = (post, store) => {
        errorMessage.value = '';

        axios.post('/api/comments', {body: store.value, post_id: post})
            .then(res => {
                comments.value.unshift(res.data.data);
            }).catch(e => {
                errorMessage.value = e.response.data.message;
            });
    }
    
    const storeRandomComment = async () => {
        await axios.post('/api/comments/storeRandomComment');
    }
    
    return {
        comment,
        comments,
        errorMessage,
        getComments,
        storeComment,
        storeRandomComment
    }
}