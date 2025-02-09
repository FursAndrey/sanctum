<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new calendar</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="calendar.title" type="text" placeholder="title" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'calendar.index'}" class="block w-48 p-2 bg-amber-600 text-white rounded-lg text-center">Return to calendars</router-link>
            <input @click.prevent="createCalendar" type="submit" value="Store" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import useCalendars from '../../../../composition/calendars';
export default {
    name: "CalendarCreate",

    setup() {
        let calendar = reactive({
            'title': '',
        });

        const { errorMessage, storeCalendar } = useCalendars();

        const createCalendar = async () => {
            await storeCalendar({...calendar});
        }

        return {
            calendar,
            errorMessage,
            createCalendar
        }
    },
}
</script>

<style scoped>

</style>
