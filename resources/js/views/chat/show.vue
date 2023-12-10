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
                    <input type="text" v-model="newMessage.body" class="w-4/5 rounded-lg text-zinc-950 p-2">
                    <span @click="createMessage()" class="ml-4 px-3 py-2 bg-sky-600 text-white rounded-lg cursor-pointer">Send</span>
                </div>
            </div>
            <div v-if="messages">
                <div v-for="message in messages.messages" :key="message.id" :class="['m-3 p-3 w-max rounded-xl border text-zinc-950', message.is_owner ? 'bg-sky-50 border-sky-400' : 'bg-green-50 border-green-400 ml-auto']">
                    <div class="text-xs italic mb-2 text-left w-max ml-auto">
                        <div>{{ message.user_name }}</div>
                        <div>{{ message.time }}</div>
                    </div>
                    <div>{{ message.body }}</div>
                </div>
                <div class="mx-auto px-3 py-2 bg-blue-500 text-white rounded-lg cursor-pointer text-center w-max" v-if="this.page < messages.lastPage">
                    load
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue';
import useChats from '../../composition/chats';
import useMessages from '../../composition/messages';
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
            page: 1,
        }
    },

    setup(props) {
        let newMessage = reactive({
            'body': '',
            'chat_id': props.id,
        });
        const { chat, getChat } = useChats();
        const { errorMessage, messages, storeMessage, getMessages } = useMessages();
        
        onMounted(getChat(props.id));
        onMounted(getMessages(props.id));

        const createMessage = async () => {
            await storeMessage({...newMessage});
            newMessage.body = '';
        }

        return {
            chat,
            newMessage,
            messages,
            createMessage,
        }
    }
}
</script>

<style scoped>
</style>