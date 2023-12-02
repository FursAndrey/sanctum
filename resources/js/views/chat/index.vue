<template>
    <div>
        <h1 class="text-3xl font-bold text-center mb-4">Chats</h1>
        <div class="flex">
            <div class="w-2/4 p-4 mr-2 bg-indigo-950 rounded-lg h-fit">
                <h3 class="font-semibold text-lg mb-6 mx-auto w-20">
                    Chat list.
                </h3>
            </div>
            <div class="w-2/4 p-4 ml-2 bg-indigo-950 rounded-lg h-fit">
                <div class="mb-6 mx-auto flex items-center justify-between">
                    <h3 class="font-semibold text-xl w-20 mr-4">
                        User list.
                    </h3>
                </div>
                <div v-if="users">
                    <div v-for="user in users" :key="user.id" class="mb-2 pb-2 border-b border-gray-300 flex items-center justify-between">
                        <div class="flex items-center justify-between w-3/5">
                            <span>
                                {{ user.name }}
                            </span>
                            <span class="px-3 py-2 bg-violet-800 text-white rounded-lg cursor-pointer">
                                Create chat
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../composition/users.js';
export default {
    name: 'index',
    
    setup() {
        const { users, getUsersForChats } = useUsers();
        
        onMounted(getUsersForChats);

        return {
            users
        }
    }
}
</script>

<style scoped>
</style>