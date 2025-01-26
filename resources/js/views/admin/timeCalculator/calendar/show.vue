<template>
    <div v-if="calendar" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ calendar.title }}</h1>
        <div>{{ calendar.discription }}</div>
        <div>
            <p>Users of this calendar:</p>
            Место для графика по данному календарю
        </div>
        <router-link :to="{ name: 'calendar.index'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to calendars</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useCalendars from '../../../../composition/calendars';
export default {
    name: "CalendarShow",

    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { calendar, getCalendar } = useCalendars();
        
        const getCurrentCalendar = () => {
            getCalendar(props.id);
        }
        
        onMounted(getCurrentCalendar);

        return {
            calendar
        }
    }
}
</script>

<style scoped>

</style>
