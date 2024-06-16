import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useBreakers() {
    const breakers = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getBreakers = async () => {
        let response = await axios.get('/api/circuitBreaker');
        breakers.value = response.data.data;
    }

    const storeBreaker = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/circuitBreaker', data);
            await router.push({ name: 'breaker.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }
    
    const destroyBreaker = async (id) => {
        await axios.delete('/api/circuitBreaker/'+id);
    }
    
    return {
        breakers,
        errorMessage,
        getBreakers,
        storeBreaker,
        destroyBreaker,
    }
}