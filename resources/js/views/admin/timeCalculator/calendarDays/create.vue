<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new calendar day</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="calendar.month_day_id" type="text" placeholder="Номер дня месяца" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <label for="work_start">Время начала рабочего дня</label>
            <input v-model="calendar.work_start" id="work_start" type="time" name="work_start" class="w-96 p-2 mb-2 border border-inherit rounded-lg text-black"/>
        </div>
        <div>
            <label for="work_end">Время окончания рабочего дня</label>
            <input v-model="calendar.work_end" id="work_end" type="time" name="work_end" class="w-96 p-2 mb-2 border border-inherit rounded-lg text-black"/>
        </div>
        <div>
            <label for="lunch_start">Время начала обеда</label>
            <input v-model="calendar.lunch_start" id="lunch_start" type="time" name="lunch_start" class="w-96 p-2 mb-2 border border-inherit rounded-lg text-black"/>
        </div>
        <div>
            <label for="lunch_end">Время окончания обеда</label>
            <input v-model="calendar.lunch_end" id="lunch_end" type="time" name="lunch_end" class="w-96 p-2 mb-2 border border-inherit rounded-lg text-black"/>
        </div>
        <div>
            <label for="control_start">Начало периода контроля</label>
            <input v-model="calendar.control_start" id="control_start" type="time" name="control_start" class="w-96 p-2 mb-2 border border-inherit rounded-lg text-black"/>
        </div>
        <div>
            <label for="control_end">Окончание периода контроля</label>
            <input v-model="calendar.control_end" id="control_end" type="time" name="control_end" class="w-96 p-2 mb-2 border border-inherit rounded-lg text-black"/>
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'calendar.show', params:{ id: calendarId }}" class="block w-48 p-2 bg-amber-600 text-white rounded-lg text-center">Return to calendar</router-link>
            <input @click.prevent="createCalendarDays" type="submit" value="Store" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import useCalendarDays from '../../../../composition/calendarDays';
import { useRoute } from 'vue-router';
export default {
    name: "CalendarDaysCreate",

    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup() {
        const route = useRoute(); // Получаем объект роута
        const calendarId = route.params.id; // Извлекаем параметр id

        let calendar = reactive({
            'month_day_id': '',
            'calendar_id': calendarId,
            'work_start': '',
            'work_end': '',
            'lunch_start': '',
            'lunch_end': '',
            'control_start': '',
            'control_end': '',
        });

        const { errorMessage, storeCalendarDays } = useCalendarDays();

        const createCalendarDays = async () => {
            await storeCalendarDays({...calendar}, calendarId);
        }

        return {
            calendarId,
            calendar,
            errorMessage,
            createCalendarDays
        }
    },
}
</script>

<style scoped>

</style>
