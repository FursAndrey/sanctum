import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useHolydays() {
    const holydays = ref([]);
    const errorMessage = ref('');
    const router = useRouter();

    const getHolydays = async () => {
        let response = await axios.get('/api/holydays');
        holydays.value = response.data.data;
    }
    
    const storeHolyday = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/holydays', data);
            await router.push({ name: 'holyday.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }

    const destroyHolyday = async (id) => {
        await axios.delete('/api/holydays/'+id);
    }
    
    return {
        holydays,
        errorMessage,
        getHolydays,
        storeHolyday,
        destroyHolyday,
    }
}