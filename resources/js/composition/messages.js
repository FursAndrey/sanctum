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

    // const getMessages = async () => {
    //     let response = await axios.get('/api/messages');
    //     messages.value = response.data;
    // }

    const storeMessage = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/messages/'+data.chat_id, data)
            .then(message => {
                console.log(message.data);
                messages.value.unshift(message.data);
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
        // getMessages,
        storeMessage,
        // updateMessage,
        // destroyMessage
    }
}