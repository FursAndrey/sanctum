import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useCalendarDays() {
    const calendarDays = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const storeCalendarDays = async (data, calendarId) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/calendarDays', data);
            await router.push({ name: 'calendar.show', params:{ id: calendarId } });
        } catch(e) {            
            errorMessage.value = e.response.data.message;
        }
    }
    
    const destroyCalendarDays = async (id) => {
        await axios.delete('/api/calendarDays/'+id);
    }
    
    return {
        calendarDays,
        errorMessage,
        storeCalendarDays,
        destroyCalendarDays
    }
}