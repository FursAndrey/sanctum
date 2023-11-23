<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Create new role</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <input v-model="role.title" type="text" placeholder="title" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <textarea v-model="role.discription" rows="4" class="block p-3 mb-2 w-full rounded-lg border border-gray-300" placeholder="Write discription your role here..."></textarea>
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'role.index'}" class="block w-48 p-2 bg-amber-600 text-white rounded-lg text-center">Return to roles</router-link>
            <input @click.prevent="createRole" type="submit" value="Store" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { reactive } from '@vue/runtime-core';
import useRoles from '../../../composition/roles';
export default {
    name: "RoleCreate",

    setup() {
        let role = reactive({
            'title': '',
            'discription': '',
        });

        const { errorMessage, storeRole } = useRoles();

        const createRole = async () => {
            await storeRole({...role});
        }

        return {
            role,
            errorMessage,
            createRole
        }
    },
}
</script>

<style scoped>

</style>
