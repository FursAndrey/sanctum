import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useCalendars() {
    const calendar = ref([]);
    const calendars = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getCalendar = async (id) => {
        let response = await axios.get('/api/calendars/' + id);
        calendar.value = response.data.data;
    }

    const getCalendars = async () => {
        let response = await axios.get('/api/calendars');
        calendars.value = response.data.data;
    }

    const storeCalendar = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/calendars', data);
            await router.push({ name: 'calendar.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }
    
    const destroyCalendar = async (id) => {
        await axios.delete('/api/calendars/'+id);
    }
    
    return {
        calendar,
        calendars,
        errorMessage,
        getCalendar,
        getCalendars,
        storeCalendar,
        destroyCalendar
    }
}