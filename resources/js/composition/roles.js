import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";

export default function useRoles() {
    const role = ref([]);
    const roles = ref([]);
    const errorMessage = ref('');
    const router = useRouter();
    
    const getRole = async (id) => {
        let response = await axios.get('/api/roles/' + id);
        role.value = response.data.data;
    }

    const getRoles = async () => {
        let response = await axios.get('/api/roles');
        roles.value = response.data.data;
    }

    const getRolesForForm = async () => {
        let response = await axios.get('/api/roles/forForm');
        roles.value = response.data.data;
    }
    
    const storeRole = async (data) => {
        errorMessage.value = '';

        try {
            await axios.post('/api/roles', data);
            await router.push({ name: 'role.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }

    const updateRole = async (id) => {
        errorMessage.value = '';
        
        try {
            await axios.put('/api/roles/' + id, {title: role.value.title, discription: role.value.discription});
            await router.push({ name: 'role.index' });
        } catch(e) {
            errorMessage.value = e.response.data.message;
        }
    }
    
    const destroyRole = async (id) => {
        await axios.delete('/api/roles/'+id);
    }
    
    return {
        role,
        roles,
        errorMessage,
        getRole,
        getRoles,
        storeRole,
        updateRole,
        destroyRole,
        getRolesForForm
    }
}