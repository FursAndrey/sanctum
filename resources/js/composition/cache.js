import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useCache() {
    const cacheClearResponse = ref([]);
    const errorMessage = ref('');
    const router = useRouter();

    const cacheClear = async () => {
        
        try {
            let response = await axios.get('/api/cacheClear');
            console.log(response.data);
            
            cacheClearResponse.value = response.data;
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }
    
    return {
        cacheClearResponse,
        errorMessage,
        cacheClear,
    }
}