<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Cache clear</h1>
        <div class="flex justify-between">
            <input @click.prevent="cacheClearFunction" type="submit" value="Cache clear" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
            
            <div v-if="cacheClearResponse.status" class="py-2 px-12 bg-lime-300 text-lime-600 rounded-lg border-4 border-solid border-lime-600">
                {{ cacheClearResponse.message }}
            </div>
        </div>
    </div>
</template>

<script>
import useCache from '../../composition/cache';
export default {
    name: 'HolydayIndex',

    data() {
        return {
            cacheClearResponse: null,
        }
    },

    setup() {
        const { cacheClearResponse, errorMessage, cacheClear } = useCache();

        const cacheClearFunction = async () => {
            await cacheClear();
        }

        return {
            cacheClearResponse,
            cacheClearFunction
        }
    },
}
</script>

<style scoped>

</style>