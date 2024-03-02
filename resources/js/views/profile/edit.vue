<template>
    <div class="w-96 mx-auto mb-16">
        <h1 class="text-3xl font-bold text-center mb-6">Edit my profile</h1>
        <div v-if="errorMessage" class="w-96 p-2 mb-2 border border-red-600 rounded-lg text-red-600 bg-red-100">
            {{ errorMessage }}
        </div>
        <p>Telegram name:</p>
        <div>
            <input v-model="user.tg_name" type="text" placeholder="Telegram name" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>

        <div class="flex justify-between">
            <router-link :to="{ name: 'profile', params:{ id: String(user.id) } }" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to profile</router-link>
            <input @click.prevent="editUser" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../composition/users';
import useInspector from '../../composition/inspector';
export default {
    name: "ProfileEdit",
    
    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { user, errorMessage, getUser, updateUser } = useUsers();
        const { isAdmin } = useInspector();

        const editUser = async () => {
            await updateUser(props.id);
        }

        const getCurrentUser = () => {
            getUser(props.id);
        }

        onMounted(getCurrentUser);

        return {
            user,
            errorMessage,
            getUser,
            editUser,
            isAdmin
        }
    },
}
</script>

<style scoped>

</style>
