import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useComments() {
    const comment = ref([]);
    const comments = ref([]);
    const errorMessage = ref('');
    
    const getComments = async (post, comment) => {
        let response = await axios.get('/api/comments/'+post+'/'+comment);
        
        comments.value = response.data.data;
    }

    const storeComment = (post, store, comment_id) => {
        errorMessage.value = '';
        
        if (comment_id == 0) {
            comment_id = null;
        }

        axios.post('/api/comments', {body: store.value, post_id: post, parent_id: comment_id})
            .then(res => {
                comments.value.unshift(res.data.data);
            }).catch(e => {
                errorMessage.value = e.response.data.message;
            });
    }
    
    const storeRandomComment = async () => {
        await axios.post('/api/comments/storeRandomComment');
    }
    
    const updateComment = async (comment) => {
        errorMessage.value = '';
        
        try {
            await axios.put('/api/comments/' + comment.id, {body: comment.body});
        } catch(e) {
            console.log(e);
            errorMessage.value = e.response.data.message;
        }
    }

    const destroyComment = (id) => {
        axios.delete('/api/comments/'+id)
        .then(res => {
            if (res.data.status === false) {
                errorMessage.value = res.data.message;
            }
        });
    }
    
    return {
        comment,
        comments,
        errorMessage,
        getComments,
        storeComment,
        updateComment,
        storeRandomComment,
        destroyComment
    }
}