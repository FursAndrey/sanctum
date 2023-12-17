import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useMessages() {
    // const message = ref([]);
    const messages = ref([]);
    const errorMessage = ref('');
    // const router = useRouter();
    
    // const getMessage = async (id) => {
    //     let response = await axios.get('/api/messages/' + id);
    //     message.value = response.data;
    // }

    const getMessages = async (id, page = 1) => {
        let response = await axios.get('/api/messages/'+id+'?page='+page);
        //если страница не первая и часть сообщений уже загружена
        if (page > 1 && messages.value.messages !== undefined) {
            for (let i = 0; i < response.data.messages.length; i++) {
                //если конкретное сообщение уже есть в чате, не рисовать его снова
                if (!messages.value.messages.some(m => m.id === response.data.messages[i].id)) {
                    messages.value.lastPage = response.data.lastPage;
                    messages.value.messages.push(response.data.messages[i]);
                    // messages.value.messages.push(...response.data.messages);
                }
            }
        } else {
            messages.value = response.data;
        }
    }

    const storeMessage = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/messages/'+data.chat_id, data)
            .then(message => {
                messages.value.messages.unshift(message.data);
            });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }

    // const updateMessage = async (id) => {
    //     errorMessage.value = '';
        
    //     try {
    //         await axios.put('/api/messages/' + id, {title: message.value.title, discription: message.value.discription});
    //         await router.push({ name: 'message.index' });
    //     } catch(e) {
    //         errorMessage.value = e.response.data.message;
    //     }
    // }
    
    // const destroyMessage = async (id) => {
    //     await axios.delete('/api/messages/'+id);
    // }
    
    return {
        // message,
        messages,
        errorMessage,
        // getMessage,
        getMessages,
        storeMessage,
        // updateMessage,
        // destroyMessage
    }
}