<template>
    <div>
        <h1 class="text-3xl font-bold text-center mb-4">
            Chats
        </h1>
        <div class="flex">
            <div :class="['w-2/4 p-4 mr-2 bg-indigo-950 rounded-lg h-fit', this.toggleUserList === true ? 'show-content' : 'hidden-content']" >
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
            <div class="min-w-fit p-2 bg-indigo-950 rounded-lg h-full hidden-mobile-content">
                <svg
                    @click="toggleContent()"
                    :class="['toggle-content cursor-pointer', this.toggleUserList === true ? 'transform-180' : '']"
                    fill="#ffffff" height="25px" width="25px" version="1.1" id="Capa_1"
                    xmlns="http://www.w3.org/2000/svg"
                    xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="-48.97 -48.97 587.64 587.64"
                    xml:space="preserve" stroke="#ffffff" stroke-width="0.004897" transform="rotate(0)matrix(1, 0, 0, 1, 0, 0)">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.9794"></g>
                    <g id="SVGRepo_iconCarrier">
                        <g>
                            <g>
                                <path d="M52.7,134.75c29.1,0,52.7-23.7,52.7-52.7s-23.6-52.8-52.7-52.8S0,52.95,0,81.95S23.7,134.75,52.7,134.75z M52.7,53.75 c15.6,0,28.2,12.7,28.2,28.2s-12.7,28.2-28.2,28.2s-28.2-12.7-28.2-28.2S37.2,53.75,52.7,53.75z"></path>
                                <path d="M52.7,297.55c29.1,0,52.7-23.7,52.7-52.7s-23.6-52.7-52.7-52.7S0,215.75,0,244.85S23.7,297.55,52.7,297.55z M52.7,216.65 c15.6,0,28.2,12.7,28.2,28.2s-12.7,28.2-28.2,28.2s-28.2-12.6-28.2-28.2S37.2,216.65,52.7,216.65z"></path>
                                <path d="M52.7,460.45c29.1,0,52.7-23.7,52.7-52.7c0-29.1-23.7-52.7-52.7-52.7S0,378.75,0,407.75C0,436.75,23.7,460.45,52.7,460.45 z M52.7,379.45c15.6,0,28.2,12.7,28.2,28.2c0,15.6-12.7,28.2-28.2,28.2s-28.2-12.7-28.2-28.2C24.5,392.15,37.2,379.45,52.7,379.45 z"></path>
                                <path d="M175.9,94.25h301.5c6.8,0,12.3-5.5,12.3-12.3s-5.5-12.3-12.3-12.3H175.9c-6.8,0-12.3,5.5-12.3,12.3 S169.1,94.25,175.9,94.25z"></path>
                                <path d="M175.9,257.15h301.5c6.8,0,12.3-5.5,12.3-12.3s-5.5-12.3-12.3-12.3H175.9c-6.8,0-12.3,5.5-12.3,12.3 S169.1,257.15,175.9,257.15z"></path>
                                <path d="M175.9,419.95h301.5c6.8,0,12.3-5.5,12.3-12.3s-5.5-12.3-12.3-12.3H175.9c-6.8,0-12.3,5.5-12.3,12.3 S169.1,419.95,175.9,419.95z"></path>
                            </g>
                        </g>
                    </g>
                </svg>
            </div>
            <div :class="['w-2/4 p-4 ml-2 bg-indigo-950 rounded-lg h-fit', this.toggleUserList === true ? 'hidden-content' : 'show-content']">
                <div class="mb-6 mx-auto flex items-center justify-between">
                    <h3 class="font-semibold text-xl w-20 mr-4">
                        User list.
                    </h3>
                </div>
                <div v-if="users">
                    <div v-for="user in users" :key="user.id" class="mb-2 pb-2 border-b border-gray-300 flex items-center justify-between">
                        <div class="flex items-center justify-between w-full">
                            <span>
                                {{ user.name }}
                            </span>
                            <span @click="createPersonalChat(user.id)" class="px-3 py-2 bg-violet-800 text-white rounded-lg cursor-pointer screen-create-chat-btn">
                                Create chat
                            </span>
                            <span @click="createPersonalChat(user.id)" class="px-3 py-2 bg-violet-800 text-white rounded-lg cursor-pointer mobile-create-chat-btn">
                                +
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import useUsers from '../../composition/users.js';
import useChats from '../../composition/chats';
import useInspector from '../../composition/inspector';
export default {
    name: 'index',

    created() {

        axios.get('/api/currentUser')
            .then( res => {
                let userId = res.data.data.id;

                window.Echo.private('user-channel-' + userId)
                .listen('.message-status', res => {
                    /** обновляем существующие чаты (количество не прочитанных сообщений и последнее сообщение) */

                    /** находим чат, который нужно обновить */
                    let tmp_chat = this.chats.find( chat => chat.id === res.chatId );
                    /** удаляем его из списка */
                    this.chats = this.chats.filter( chat => chat.id !== res.chatId );
                    /** обновляем его во временной переменной */
                    tmp_chat.unreadable_messages_count = res.countMessages;
                    tmp_chat.last_message = res.message;
                    /** добавляем в верх списка */
                    this.chats.unshift(tmp_chat);
                });

                window.Echo.private('store-first-message-channel-' + userId)
                .listen('.store-first-message', res => {
                    /** добавляем новый чат (и сообщение) */
                    this.chats.unshift(res.chat);
                });

                window.Echo.private('destroy-chat-channel-' + userId)
                .listen('.destroy-chat', res => {
                    /** убираем чат из списка (если кто-то из участников его удалил) */
                    this.chats = this.chats.filter( chat => chat.id !== res.chat );
                });
            });
    },

    setup() {
        const toggleUserList = ref(true);
        const { users, getUsersExceptMe } = useUsers();
        const { errorMessage, chats, storeChat, getChats } = useChats();
        const { hasBanChat } = useInspector();

        onMounted(getUsersExceptMe);
        onMounted(getChats);

        const createPersonalChat = async (targetUserId) => {
            if (hasBanChat() === true) {
                return false;
            }
            await storeChat({ title: null, users: [targetUserId] });
        }

        const toggleContent = () => {
            toggleUserList.value = !toggleUserList.value;
        }

        return {
            users,
            chats,
            toggleUserList,
            createPersonalChat,
            toggleContent
        }
    }
}
</script>

<style scoped>
.transform-180 {
    transform: rotate(180deg);
}
span.mobile-create-chat-btn
, svg.toggle-content
, div.hidden-mobile-content {
    display: none;
}

@media (max-width: 1200px) {
    /* контент на всю ширину страницы */
    div#app > div > div.mx-auto {
        width: 95%;
    }

    /* замена кнопок добавления чатов */
    span.screen-create-chat-btn {
        display: none;
    }
    span.mobile-create-chat-btn {
        display: block;
    }

    /* расширяем блок "логины пользователей + кнопки создания чатов" */
    div.mb-2.pb-2.border-b.border-gray-300.flex.items-center.justify-between > div.flex.items-center.justify-between {
        width: 95%;
    }
}
@media (max-width: 750px) {
    /* показать кнопку для переключения списков чатов/пользователей */
    svg.toggle-content {
        display: inline-block;
    }
    /* скрываем по клику или список чатов или список пользователей */
    div.hidden-content {
        display: none;
    }
    div.show-content {
        width: 90%;
    }

    /* показываем что рядом есть что-то еще */
    div.hidden-mobile-content {
        display: block;
    }
}
</style>