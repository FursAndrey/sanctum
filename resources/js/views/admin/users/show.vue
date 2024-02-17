<template>
    <div v-if="user" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ user.name }}</h1>
        <div>{{ user.email }}</div>
        <div class="text-sm text-right text-slate-400">{{ user.created }}</div>
        <div>
            <p>My roles:</p>
            <div v-for="role in user.roles" :key="role.id">
                {{ role.title }}
            </div>
        </div>
        <div>
            <p>Блокировки:</p>
            <div v-if="user.ban_chat || user.ban_comment">
                <span v-if="user.ban_chat" class="text-left block">Чаты заблокированы: {{ user.ban_chat }}</span>
                <span v-if="user.ban_comment" class="text-left block">Комментарии заблокированы: {{ user.ban_comment }}</span>
            </div>
            <div v-else>отсутствуют</div>
        </div>
        <router-link :to="{ name: 'user.index'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to users</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../../composition/users';
export default {
    name: "UserShow",

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
