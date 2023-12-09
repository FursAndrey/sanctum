import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useChats() {
    const chat = ref([]);
    const chats = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getChat = async (id) => {
        let response = await axios.get('/api/chats/' + id);
        chat.value = response.data;
    }

    const getChats = async () => {
        let response = await axios.get('/api/chats');
        chats.value = response.data;
    }

    const storeChat = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/chats', data)
            .then(chat => {
                router.push({ name: 'chats.show', params:{ id: String(chat.data.id) } });
            });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }

    // const updateChat = async (id) => {
    //     errorMessage.value = '';
        
    //     try {
    //         await axios.put('/api/chats/' + id, {title: chat.value.title, discription: chat.value.discription});
    //         await router.push({ name: 'chat.index' });
    //     } catch(e) {
    //         errorMessage.value = e.response.data.message;
    //     }
    // }
    
    // const destroyChat = async (id) => {
    //     await axios.delete('/api/chats/'+id);
    // }
    
    return {
        chat,
        chats,
        errorMessage,
        getChat,
        getChats,
        storeChat,
        // updateChat,
        // destroyChat
    }
}