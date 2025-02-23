import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useCalendarUser() {
    const calendarUser = ref([]);
    const calendars = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getCalendars = async () => {
        let response = await axios.get('/api/calendars');
        calendars.value = response.data.data;
    }

    const getCalendarUser = async (userId) => {
        // let response = await axios.get('/api/calendars');
        // calendars.value = response.data.data;
        calendarUser.value = null;
    }

    const storeCalendarUser = async (userId, calendarId) => {
        errorMessage.value = '';
        const data = {
            user_id: userId,
            calendar_id: calendarId
        }

        try {
            await axios.post('/api/calendarUser', data);
            await router.push({ name: 'user.show', params:{ id: userId } });
        } catch(e) {            
            errorMessage.value = e.response.data.message;
        }
    }

    return {
        calendarUser,
        calendars,
        errorMessage,
        storeCalendarUser,
        getCalendarUser,
        getCalendars
    }
}