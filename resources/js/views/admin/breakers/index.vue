<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Автоматические выключатели</h1>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-4 lg:-mx-6">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <router-link :to="{ name: 'breaker.create'}" class="block mx-auto mb-4 w-48 p-2 font-bold bg-lime-600 text-white rounded-lg text-center">Create new выключатель</router-link>
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center text-sm font-light">
                            <thead class="border-b bg-neutral-800 font-medium text-white dark:border-neutral-500 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class="px-6 py-4">Номинал</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="breaker in breakers" :key="breaker.id" class="border-b dark:border-neutral-500">
                                    <td class="whitespace-nowrap px-6 py-4 text-left">
                                        {{ breaker.nominal }} A
                                    </td>
                                    <td>
                                        <span @click="deleteBreaker(breaker.id)" class="p-2 me-6 font-bold bg-red-700 text-white rounded-lg text-center cursor-pointer">Delete</span>
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
import useBreakers from '../../../composition/breakers';
export default {
    name: 'BreakerIndex',

    setup() {
        const { breakers, getBreakers, destroyBreaker } = useBreakers();

        onMounted(getBreakers);

        const deleteBreaker = async (id) => {
            if (!window.confirm('Are you sure?')) {
                return false;
            }

            await destroyBreaker(id);
            await getBreakers();
        }

        return {
            breakers,
            deleteBreaker
        }
    },
}
</script>

<style scoped>

</style>