<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new holyday</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="holyday.holyday" type="text" placeholder="holyday" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'holyday.index'}" class="block w-48 p-2 bg-amber-600 text-white rounded-lg text-center">Return to holydays</router-link>
            <input @click.prevent="createHolyday" type="submit" value="Store" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import useHolydays from '../../../../composition/holydays';
export default {
    name: "HolydayCreate",

    setup() {
        let holyday = reactive({
            'holyday': '',
        });

        const { errorMessage, storeHolyday } = useHolydays();

        const createHolyday = async () => {
            await storeHolyday({...holyday});
        }

        return {
            holyday,
            errorMessage,
            createHolyday
        }
    },
}
</script>

<style scoped>

</style>
