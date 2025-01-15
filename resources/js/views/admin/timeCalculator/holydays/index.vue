<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Holydays</h1>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-4 lg:-mx-6">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <router-link :to="{ name: 'holyday.create'}" class="block mx-auto mb-4 w-48 p-2 font-bold bg-lime-600 text-white rounded-lg text-center">Create new holyday</router-link>
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center text-sm font-light">
                            <thead class="border-b bg-neutral-800 font-medium text-white dark:border-neutral-500 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Holyday</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="holyday in holydays" :key="holyday.id" class="border-b dark:border-neutral-500">
                                    <td class="whitespace-nowrap px-6 py-4 text-left">
                                        {{ holyday.holyday }}
                                    </td>
                                    <td>
                                        <span @click="deleteHolyday(holyday.id)" class="p-2 me-6 font-bold bg-red-700 text-white rounded-lg text-center cursor-pointer">Delete</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useHolydays from '../../../../composition/holydays';
export default {
    name: 'HolydayIndex',

    data() {
        return {
            holydays: null,
        }
    },

    setup() {
        const { holydays, getHolydays, destroyHolyday } = useHolydays();

        onMounted(getHolydays);

        const deleteHolyday = async (id) => {
            if (!window.confirm('Are you sure?')) {
                return false;
            }

            await destroyHolyday(id);
            await getHolydays();
        }

        return {
            holydays,
            deleteHolyday
        }
    },
}
</script>

<style scoped>

</style>