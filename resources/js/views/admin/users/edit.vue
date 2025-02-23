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
        <p>Telegram name:</p>
        <div>
            <input v-model="user.tg_name" type="text" placeholder="Telegram name" class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div class="my-4">
            <p>Баны:</p>
            <input class="cursor-pointer" type="checkbox" :value="user.has_ban_chat" v-model="user.has_ban_chat" id="ban_chat">
            <label class="ml-3 cursor-pointer" for="ban_chat">Блокировка чата <span v-if="user.ban_chat">с {{ user.ban_chat }}</span></label><br>
            <input class="cursor-pointer" type="checkbox" :value="user.has_ban_comment" v-model="user.has_ban_comment" id="ban_comment">
            <label class="ml-3 cursor-pointer" for="ban_comment">Блокировка комментариев <span v-if="user.ban_comment">с {{ user.ban_comment }}</span></label><br>
        </div>
        <div class="flex justify-between">
            <router-link :to="{ name: 'user.index'}" class="block w-48 p-2 font-bold bg-amber-600 text-white rounded-lg text-center">Return to users</router-link>
            <input @click.prevent="editUser" type="submit" value="Update" class="w-32 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
        <div class="my-4">
            <p>Календари:</p>
            <input class="cursor-pointer" name="calendar_id" type="radio" :value="null" v-model="calendarUser" id="calendar_0">
            <label class="ml-3 cursor-pointer" for="calendar_0">Удалить календарь</label><br>
            <div v-for="calendar in calendars" :key="calendar.id">
                <input class="cursor-pointer" name="calendar_id" type="radio" :value="calendar.id" v-model="calendarUser" :id="'calendar_' + calendar.id">
                <label class="ml-3 cursor-pointer" :for="'calendar_' + calendar.id">{{ calendar.title }}</label><br>
            </div>
            <input @click.prevent="setCalendarUser" type="submit" value="Set calendar" class="w-32 mt-4 p-2 bg-lime-600 text-white rounded-lg cursor-pointer">
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../../composition/users';
import useRoles from '../../../composition/roles';
import useCalendarUser from '../../../composition/calendarUser';
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
        const { calendars, calendarUser, getCalendars, getCalendarUser, storeCalendarUser } = useCalendarUser();

        const editUser = async () => {
            await updateUser(props.id);
        }

        const getCurrentUser = () => {
            getUser(props.id);
        }

        const setCalendarUser = () => {
            storeCalendarUser(props.id, calendarUser.value);
        }

        onMounted(getCurrentUser);
        onMounted(getRolesForForm);
        onMounted(getCalendars);
        onMounted(getCalendarUser(props.id));

        return {
            user,
            roles,
            calendars,
            calendarUser,
            errorMessage,
            getUser,
            editUser,
            setCalendarUser
        }
    },
}
</script>

<style scoped>

</style>
