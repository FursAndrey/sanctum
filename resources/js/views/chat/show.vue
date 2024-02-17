<template>
    <div>
        <h1 class="text-3xl font-bold text-center mb-4">Message list</h1>
        <div v-if="chat" class="p-4 mr-2 bg-indigo-950 rounded-lg h-fit">
            <div class="flex justify-between">
                <h3 class="font-semibold text-lg mb-6 mx-auto w-40">
                    {{ chat.title }}
                </h3>
                <svg @click="deleteChat()" class="cursor-pointer" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="20" height="20" viewBox="0,0,256,256">
                    <g fill="#fffdfd" 
                        fill-rule="nonzero" 
                        stroke="none" 
                        stroke-width="1" 
                        stroke-linecap="butt" 
                        stroke-linejoin="miter" 
                        stroke-miterlimit="10" 
                        stroke-dasharray="" 
                        stroke-dashoffset="0" 
                        font-family="none" 
                        font-weight="none" 
                        font-size="none" 
                        text-anchor="none" 
                        style="mix-blend-mode: normal">
                        <g transform="scale(5.12,5.12)">
                            <path d="M42,5h-10v-2c0,-1.65234 -1.34766,-3 -3,-3h-8c-1.65234,0 -3,1.34766 -3,3v2h-10c-0.55078,0 -1,0.44922 -1,1c0,0.55078 0.44922,1 1,1h1.08594l3.60938,40.51563c0.125,1.39063 1.30859,2.48438 2.69531,2.48438h19.21484c1.38672,0 2.57031,-1.09375 2.69531,-2.48437l3.61328,-40.51562h1.08594c0.55469,0 1,-0.44922 1,-1c0,-0.55078 -0.44531,-1 -1,-1zM20,44c0,0.55469 -0.44922,1 -1,1c-0.55078,0 -1,-0.44531 -1,-1v-33c0,-0.55078 0.44922,-1 1,-1c0.55078,0 1,0.44922 1,1zM20,3c0,-0.55078 0.44922,-1 1,-1h8c0.55078,0 1,0.44922 1,1v2h-10zM26,44c0,0.55469 -0.44922,1 -1,1c-0.55078,0 -1,-0.44531 -1,-1v-33c0,-0.55078 0.44922,-1 1,-1c0.55078,0 1,0.44922 1,1zM32,44c0,0.55469 -0.44531,1 -1,1c-0.55469,0 -1,-0.44531 -1,-1v-33c0,-0.55078 0.44531,-1 1,-1c0.55469,0 1,0.44922 1,1z"></path>
                        </g>
                    </g>
                </svg>
            </div>
            <div class="mb-6">
                <h3 class="font-semibold text-lg mb-2">
                    Send messages
                </h3>
                <div  ref="observerInputFixed">
                    <input type="text" v-model="newMessage.body" class="w-4/5 rounded-lg text-zinc-950 p-2">
                    <span @click="createMessage()" class="ml-4 px-3 py-2 bg-sky-600 text-white rounded-lg cursor-pointer">Send</span>
                </div>
                <div  :class="['', this.fixedInput === true ? 'fixedInput' : 'unfixedInput']">
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
        <div ref="observerLoadMoreMessages"></div>
    </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue';
import useChats from '../../composition/chats';
import useMessages from '../../composition/messages';
import useInspector from '../../composition/inspector';
export default {
    name: 'index',

    props: {
        id: {
            required: true,
            type: String
        }
    },

    created() {
        //входящие сообщения для остальных участников чатов
        window.Echo.private('store-message-channel-' + this.$route.params.id)
        .listen('.store-message', res => {
            this.messages.messages.unshift(res.message);

            if (this.$route.fullPath === '/chats/' + this.$route.params.id) {
                axios.put('/api/messageUsers/'+this.$route.params.id, { message_id: res.message.id })
            }
        })
    },

    mounted() {
        const optionLoadMoreMessages = {
            root: null,         //наблюдать за областью просмотра браузера
            rootMargin: '0px',  //отступ вокруг root-элемента, когда он задан (CSS-margin)
            threshold: 1.0      //от 0 до 1, 0 - если виден хоть 1 пиксель - запустить callback, 1 - если видны все пиксели - запустить callback
        }
        //что делать при обнаружении наблюдаемого объекта
        const callbackLoadMoreMessages = (entries, observerLoadMoreMessages) => {
            //работать только при появлении наблюдаемого объекта (при скрытии - ничего)
            if (entries[0].isIntersecting && this.haveMoreMessages) {
                this.loadNextPageMessages();
            }
        }
        //инициализация наблюдателю
        const observerLoadMoreMessages = new IntersectionObserver(callbackLoadMoreMessages, optionLoadMoreMessages);
        //привязываем наблюдателя к наблюдаемому огбъекту
        observerLoadMoreMessages.observe(this.$refs.observerLoadMoreMessages);

        const optionInputFixed = {
            root: null,         //наблюдать за областью просмотра браузера
            rootMargin: '0px',  //отступ вокруг root-элемента, когда он задан (CSS-margin)
            threshold: 0      //от 0 до 1, 0 - если виден хоть 1 пиксель - запустить callback, 1 - если видны все пиксели - запустить callback
        }
        //что делать при обнаружении наблюдаемого объекта
        const callbackInputFixed = (entries, observerInputFixed) => {
            // //работать только при появлении наблюдаемого объекта
            if (entries[0].isIntersecting === true && this.fixedInput === true) {
                this.fixedInput = false;
            }
            if (entries[0].isIntersecting === false && this.fixedInput === false) {
                this.fixedInput = true;
            }
        }
        //инициализация наблюдателю
        const observerInputFixed = new IntersectionObserver(callbackInputFixed, optionInputFixed);
        //привязываем наблюдателя к наблюдаемому огбъекту
        observerInputFixed.observe(this.$refs.observerInputFixed);
    },

    setup(props) {
        const fixedInput = ref(true);
        let newMessage = reactive({
            'body': '',
            'chat_id': props.id,
        });
        let currentPage = ref(1);
        let haveMoreMessages = ref(true);

        const { chat, getChat, destroyChat } = useChats();
        const { errorMessage, messages, storeMessage, getMessages } = useMessages();
        const { hasBanChat } = useInspector();

        onMounted(getChat(props.id));
        onMounted(getMessages(props.id));

        const loadNextPageMessages = () => {
            getMessages(props.id, ++currentPage.value);
            if (messages.value.lastPage <= currentPage.value) {
                haveMoreMessages.value = false;
            }
        }

        const createMessage = async () => {
            if (hasBanChat() === true) {
                return false;
            }
            await storeMessage({...newMessage});
            newMessage.body = '';
        }

        const deleteChat = async () => {
            if (hasBanChat() === true) {
                return false;
            }
            await destroyChat(props.id);
        }

        return {
            chat,
            currentPage,
            newMessage,
            messages,
            haveMoreMessages,
            fixedInput,
            createMessage,
            loadNextPageMessages,
            deleteChat
        }
    }
}
</script>

<style scoped>
.fixedInput {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: #003475;
    padding: 10px 15px;
    margin-bottom: 0;
}
.unfixedInput {
    position: relative;
    display: none;
}

@media (max-width: 750px) {
    /* контент на всю ширину страницы */
    div#app > div > div.mx-auto {
        width: 95%;
    }
}

@media (max-width: 500px) {
    /* уменьшить ширину поля ввода текста */
    input.rounded-lg.text-zinc-950.p-2 {
        width: 60%;
    }
}
</style>