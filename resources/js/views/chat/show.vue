<template>
    <div>
        <h1 class="text-3xl font-bold text-center mb-4">Message list</h1>
        <div v-if="chat" class="p-4 mr-2 bg-indigo-950 rounded-lg h-fit">
            <h3 class="font-semibold text-lg mb-6 mx-auto w-40">
                {{ chat.title }}
            </h3>
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-2">
                    Send messages
                </h3>
                <div>
                    <input type="text" v-model="body" class="w-3/4 rounded-lg">
                    <span class="ml-4 px-3 py-2 bg-sky-600 text-white rounded-lg cursor-pointer">Send</span>
                </div>
            </div>
            <div v-if="chat.messages">
                <div v-for="message in chat.messages" :key="message.id" :class="['m-3 p-3 w-max rounded-xl border', message.is_owner ? 'bg-sky-50 border-sky-400' : 'bg-green-50 border-green-400 ml-auto']">
                    <div class="text-xs italic mb-2 text-left w-max ml-auto">
                        <div>{{ message.user_name }}</div>
                        <div>{{ message.time }}</div>
                    </div>
                    <div>{{ message.body }}</div>
                </div>
                <div class="mx-auto px-3 py-2 bg-blue-500 text-white rounded-lg cursor-pointer text-center w-max" v-if="this.page < this.lastPage">
                    load
                </div>
            </div>
            <div>
                {{ chat }}
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import useChats from '../../composition/chats';
export default {
    name: 'index',
    
    props: {
        id: {
            required: true,
            type: String
        }
    },
    
    data() {
        return {
            body: '',
            page: 1,
            lastPage: 10,
        }
    },

    setup(props) {
        const { errorMessage, chat, getChat } = useChats();
        
        onMounted(getChat(props.id));

        return {
            chat,
        }
    }
}
</script>

<style scoped>
</style>