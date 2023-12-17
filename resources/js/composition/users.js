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

    const getUsersExceptMe = async () => {
        let response = await axios.get('/api/users/exceptMe');
        users.value = response.data.data;
    }

    const updateUser = async (id) => {
        errorMessage.value = '';

        try {
            const lastPath = router.options.history.state.back;
            await axios.put('/api/users/' + id, { roles: user.value.roles, tg_name: user.value.tg_name });
            await router.push({ path: lastPath });
        } catch (e) {
            errorMessage.value = e.response.data.message;
        }
    }

    const destroyUser = async (id) => {
        await axios.delete('/api/users/' + id);
    }

    const storeRandomUser = async () => {
        await axios.post('/api/users/storeRandomUser')
            .then(res => {
                getUsers();
                // }).catch(e => {
                //     console.log(e);
            });
    }

    return {
        user,
        users,
        errorMessage,
        getUser,
        getUsers,
        getUsersExceptMe,
        updateUser,
        destroyUser,
        storeRandomUser
    }
}