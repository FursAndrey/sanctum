<template>
    <div v-if="user" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">My profile</h1>
        <div class="text-sm text-right text-slate-400">{{ user.created }}</div>
        <div>My name: {{ user.name }}</div>
        <div>My email: {{ user.email }}</div>
        <div>
            <p>My roles:</p>
            <div v-for="role in user.roles" :key="role.id" class="indent-1.5">
                {{ role.title }}
            </div>
        </div>
        <router-link :to="{ name: 'profile.edit', params:{ id: String(user.id) } }" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Edit profile</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../composition/users';
export default {
    name: "ProfileShow",

    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { user, getUser } = useUsers();
        
        const getCurrentUser = () => {
            getUser(props.id);
        }
        
        onMounted(getCurrentUser);

        return {
            user
        }
    }
}
</script>

<style scoped>

</style>
