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
            </div>
        </div>
        <div ref="observer"></div>
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

    created() {
        window.Echo.private('store-message-channel-' + this.$route.params.id)
        .listen('.store-message', res => {
            this.messages.messages.unshift(res.message);

            if (this.$route.fullPath === '/chats/' + this.$route.params.id) {   
                axios.put('/api/messageUsers/'+this.$route.params.id, { message_id: res.message.id })
            }
        })
    },

    mounted() {
        const option = {
            root: null,         //наблюдать за областью просмотра браузера
            rootMargin: '0px',  //отступ вокруг root-элемента, когда он задан (CSS-margin)
            threshold: 1.0      //от 0 до 1, 0 - если виден хоть 1 пиксель - запустить callback, 1 - если видны все пиксели - запустить callback
        }
        //что делать при обнаружении наблюдаемого объекта
        const callback = (entries, observer) => {
            //работать только при появлении наблюдаемого объекта (при скрытии - ничего)
            if (entries[0].isIntersecting && this.haveMoreMessages) {
                this.loadNextPageMessages();
            }
        }
        //инициализация наблюдателю
        const observer = new IntersectionObserver(callback, option);
        //привязываем наблюдателя к наблюдаемому огбъекту
        observer.observe(this.$refs.observer);
    },

    setup(props) {
        let newMessage = reactive({
            'body': '',
            'chat_id': props.id,
        });
        let currentPage = ref(1);
        let haveMoreMessages = ref(true);

        const { chat, getChat } = useChats();
        const { errorMessage, messages, storeMessage, getMessages } = useMessages();

        onMounted(getChat(props.id));
        onMounted(getMessages(props.id));

        const loadNextPageMessages = () => {
            getMessages(props.id, ++currentPage.value);
            if (messages.value.lastPage <= currentPage.value) {
                haveMoreMessages.value = false;
            }
        }

        const createMessage = async () => {
            await storeMessage({...newMessage});
            newMessage.body = '';
        }

        return {
            chat,
            currentPage,
            newMessage,
            messages,
            haveMoreMessages,
            createMessage,
            loadNextPageMessages,
        }
    }
}
</script>

<style scoped>
</style>