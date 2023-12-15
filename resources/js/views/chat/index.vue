<template>
    <div>
        <h1 class="text-3xl font-bold text-center mb-4">Chats</h1>
        <div class="flex">
            <div class="w-2/4 p-4 mr-2 bg-indigo-950 rounded-lg h-fit">
                <h3 class="font-semibold text-lg mb-6 mx-auto w-20">
                    Chat list.
                </h3>
                <div v-if="chats">
                    <div v-for="chat in chats" :key="chat.id" class="mb-2 pb-2 border-b border-gray-300 w-full">
                        <router-link :to="{ name: 'chats.show', params:{ id: String(chat.id) }}">
                            <div class="flex justify-between w-full mb-2">
                                <div>
                                    <span>
                                        {{ chat.id }}
                                    </span>
                                    <span class="ml-4 font-semibold">
                                        {{ chat.title ?? 'Your chat' }}
                                    </span>
                                </div>
                                <div v-if="chat.unreadable_messages_count > 0" class="px-2 py-1 bg-green-500 rounded-full text-white text-xs">
                                    {{ chat.unreadable_messages_count }}
                                </div>
                            </div>
                            <div v-if="chat.unreadable_messages_count > 0" :class="['ml-10 w-11/12 text-zinc-500 p-2 rounded-lg', chat.unreadable_messages_count > 0 ? 'bg-gray-100' : '']">
                                <div class="flex justify-between w-full">
                                    <div class="text-xs w-max">{{ chat.last_message.user_name }}</div>
                                    <div class="text-xs italic w-max">{{ chat.last_message.time }}</div>
                                </div>
                                <div>{{ chat.last_message.body }}</div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
            <div class="w-2/4 p-4 ml-2 bg-indigo-950 rounded-lg h-fit">
                <div class="mb-6 mx-auto flex items-center justify-between">
                    <h3 class="font-semibold text-xl w-20 mr-4">
                        User list.
                    </h3>
                </div>
                <div v-if="users">
                    <div v-for="user in users" :key="user.id" class="mb-2 pb-2 border-b border-gray-300 flex items-center justify-between">
                        <div class="flex items-center justify-between w-3/5">
                            <span>
                                {{ user.name }}
                            </span>
                            <span @click="createPersonalChat(user.id)" class="px-3 py-2 bg-violet-800 text-white rounded-lg cursor-pointer">
                                Create chat
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../composition/users.js';
import useChats from '../../composition/chats';
export default {
    name: 'index',
    
    created() {
        
        axios.get('/api/currentUser')
            .then( res => {
                let userId = res.data.data.id;

                window.Echo.private('user-channel-' + userId)
                .listen('.message-status', res => {
                    this.chats.filter( chat => {
                        if (chat.id === res.chatId) {
                            chat.unreadable_messages_count = res.countMessages;
                            chat.last_message = res.message;
                        }
                    });
                })
            })
    },

    setup() {
        const { users, getUsersExceptMe } = useUsers();
        const { errorMessage, chats, storeChat, getChats } = useChats();
        
        onMounted(getUsersExceptMe);
        onMounted(getChats);

        const createPersonalChat = async (targetUserId) => {
            await storeChat({ title: null, users: [targetUserId] });
        }

        return {
            users,
            chats,
            createPersonalChat
        }
    }
}
</script>

<style scoped>
</style>