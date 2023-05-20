<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Users</h1>
        <div class="flex flex-col">
            <div class="overflow-x-auto sm:-mx-4 lg:-mx-6">
                <div class="inline-block min-w-full py-2 sm:px-6 lg:px-8">
                    <div class="overflow-hidden">
                        <table class="min-w-full text-center text-sm font-light">
                            <thead class="border-b bg-neutral-800 font-medium text-white dark:border-neutral-500 dark:bg-neutral-900">
                                <tr>
                                    <th scope="col" class=" px-6 py-4">Name</th>
                                    <th scope="col" class=" px-6 py-4">Email</th>
                                    <th scope="col" class=" px-6 py-4">Created</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id" class="border-b dark:border-neutral-500">
                                    <td class="whitespace-nowrap px-6 py-4 text-left">
                                        <router-link :to="{ name: 'user.show', params:{ id: user.id } }" class="p-2 me-6 font-bold bg-sky-700 text-white rounded-lg text-center">learn more</router-link>
                                        {{ user.name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ user.email }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">{{ user.created }}</td>
                                    <td>
                                        <!-- <router-link :to="{ name: 'user.edit', params:{ id: user.id } }" class="p-2 me-6 font-bold bg-amber-600 text-white rounded-lg text-center">Edit</router-link> -->
                                        <span @click="deleteUser(user.id)" class="p-2 me-6 font-bold bg-red-700 text-white rounded-lg text-center cursor-pointer">Delete</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from 'vue';
import useUsers from '../../../composition/users';
export default {
    name: 'UserIndex',

    data() {
        return {
            users: null,
        }
    },

    setup() {
        const { users, getUsers, destroyUser } = useUsers();

        onMounted(getUsers);

        const deleteUser = async (id) => {
            if (!window.confirm('Are you sure?')) {
                return false;
            }

            await destroyUser(id);
            await getUsers();
        }

        return {
            users,
            deleteUser
        }
    },
}
</script>

<style scoped>

</style>