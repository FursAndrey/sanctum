<template>
    <div v-if="calendar" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">Информация о календаре {{ calendar.title }}</h1>
        <div>
            <router-link v-if="calendar.id" :to="{ name: 'calendarDays.create', params:{ id: calendar.id }}" class="block mx-auto mb-4 w-60 p-2 font-bold bg-lime-600 text-white rounded-lg text-center">
                Create new calendar day
            </router-link>
            <table class="min-w-full text-center text-sm font-light">
                <thead class="border-b bg-neutral-800 font-medium text-white dark:border-neutral-500 dark:bg-neutral-900">
                    <tr>
                        <th scope="col" class="px-6 py-4">Day</th>
                        <th scope="col" class="px-6 py-4">work start</th>
                        <th scope="col" class="px-6 py-4">work end</th>
                        <th scope="col" class="px-6 py-4">lunch start</th>
                        <th scope="col" class="px-6 py-4">lunch end</th>
                        <th scope="col" class="px-6 py-4">control start</th>
                        <th scope="col" class="px-6 py-4">control end</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="day in calendar.calendarDays" :key="day.id" class="border-b dark:border-neutral-500">
                        <td class="px-6 py-4 text-left">
                            {{ day.month_day_id }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ day.work_start }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ day.work_end }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ day.lunch_start }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ day.lunch_end }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ day.control_start }}
                        </td>
                        <td class="px-6 py-4 text-left">
                            {{ day.control_end }}
                        </td>
                        <td>
                            <span @click="deleteCalendarDays(day.id)" class="p-2 me-6 font-bold bg-red-700 text-white rounded-lg text-center cursor-pointer">Delete</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <router-link :to="{ name: 'calendar.index'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to calendars</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useCalendars from '../../../../composition/calendars';
import useCalendarDays from '../../../../composition/calendarDays';
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
        const { calendarDays, destroyCalendarDays } = useCalendarDays();
        
        const getCurrentCalendar = () => {
            getCalendar(props.id);
        }
        
        onMounted(getCurrentCalendar);

        const deleteCalendarDays = async (id) => {
            if (!window.confirm('Are you sure?')) {
                return false;
            }

            await destroyCalendarDays(id);
            getCurrentCalendar()
        }

        return {
            calendar,
            calendarDays,
            deleteCalendarDays
        }
    }
}
</script>

<style scoped>

</style>
