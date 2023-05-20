<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Edit this role</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div>
            <textarea v-model="role.discription" rows="4" class="block p-3 mb-2 w-full rounded-lg border border-gray-300" placeholder="Write discription your role here..."></textarea>
        </div>
        
        <div class="flex justify-between">
            <router-link :to="{ name: 'role.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to roles</router-link>
            <input @click.prevent="editRole" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useRoles from '../../../composition/roles';
export default {
    name: "RoleEdit",
    
    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { role, errorMessage, getRole, updateRole } = useRoles();

        const editRole = async () => {
            await updateRole(props.id);
        }

        const getCurrentRole = () => {
            getRole(props.id);
        }
        
        onMounted(getCurrentRole);

        return {
            role,
            errorMessage,
            getRole,
            editRole
        }
    },
}
</script>

<style scoped>

</style>
