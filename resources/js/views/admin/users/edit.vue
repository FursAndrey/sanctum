<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Edit this user: {{ user.email }}</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <div class="my-4">
            <p>All roles:</p>
            <div v-for="role in roles" :key="role.id">
                <input class="cursor-pointer" type="checkbox" :value="role" v-model="user.roles" :id="'role' + role.id">
                <label class="ml-3 cursor-pointer" :for="'role' + role.id">{{ role.title }}</label><br>
            </div>
        </div>
        
        <div class="flex justify-between">
            <router-link :to="{ name: 'user.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to users</router-link>
            <input @click.prevent="editUser" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../../composition/users';
import useRoles from '../../../composition/roles';
export default {
    name: "UserEdit",
    
    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { user, errorMessage, getUser, updateUser } = useUsers();
        const { roles, getRolesForForm } = useRoles();

        const editUser = async () => {
            await updateUser(props.id);
        }

        const getCurrentUser = () => {
            getUser(props.id);
        }

        onMounted(getCurrentUser);
        onMounted(getRolesForForm);

        return {
            user,
            roles,
            errorMessage,
            getUser,
            editUser
        }
    },
}
</script>

<style scoped>

</style>
