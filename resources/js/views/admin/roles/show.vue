<template>
    <div v-if="role" class="mx-auto mb-16 px-8">
        <h1 class="text-3xl font-bold text-center mb-6">{{ role.title }}</h1>
        <div>{{ role.discription }}</div>
        <div>
            <p>Users of this role:</p>
            <div v-for="user in role.users" :key="user.id">
                {{ user.email }}
            </div>
        </div>
        <router-link :to="{ name: 'role.index'}" class="block w-48 p-2 font-bold bg-sky-700 text-white rounded-lg text-center mt-10">Return to roles</router-link>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useRoles from '../../../composition/roles';
export default {
    name: "RoleShow",

    props: {
        id: {
            required: true,
            type: String
        }
    },

    setup(props) {
        const { role, getRole } = useRoles();
        
        const getCurrentRole = () => {
            getRole(props.id);
        }
        
        onMounted(getCurrentRole);

        return {
            role
        }
    }
}
</script>

<style scoped>

</style>
