import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useUsers() {
    const user = ref([]);
    const users = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getUser = async (id) => {
        let response = await axios.get('/api/users/' + id);
        user.value = response.data.data;
    }

    const getUsers = async () => {
        let response = await axios.get('/api/users');
        users.value = response.data.data;
    }
    
    const updateUser = async (id) => {
        errorMessage.value = '';
        
        try {
            await axios.put('/api/users/' + id, {roles: user.value.roles});
            await router.push({ name: 'user.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }
    
    const destroyUser = async (id) => {
        await axios.delete('/api/users/'+id);
    }
    
    return {
        user,
        users,
        errorMessage,
        getUser,
        getUsers,
        updateUser,
        destroyUser
    }
}