import { ref } from "vue";
import axios from "axios";

export default function useLikes() {
    const like = ref([]);
    
    const togglePostLike = async (post) => {
        await axios.post('/api/postLike/'+post)
            .then(res => {
                like.value = res.data;
            });
    }

    const toggleCommentLike = async (comment) => {
        await axios.post('/api/commentLike/'+comment)
            .then(res => {
                like.value = res.data;
            });
    }
    
    return {
        like,
        togglePostLike,
        toggleCommentLike,
    }
}