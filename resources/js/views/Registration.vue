<template>
    <div class="w-96 mx-auto mb-16 mt-8">
        <div>
            <input v-model="name" type="name" placeholder="name"
                    class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <input v-model="email" type="email" placeholder="email"
                    class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <input v-model="password" type="password" placeholder="password"
                    class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <div>
            <input v-model="password_confirmation" type="password" placeholder="password_confirmation"
                    class="w-96 p-2 mb-2 border border-inherit rounded-lg">
        </div>
        <input @click.prevent="register" type="submit" value="register"
                class="block mx-auto w-32 p-2 bg-sky-700 text-white rounded-lg cursor-pointer">
    </div>
</template>

<script>
export default {
    name: "registration",

    data() {
        return {
            name: null,
            email: null,
            password: null,
            password_confirmation: null,
        }
    },

    methods: {
        register() {
            axios.get('/sanctum/csrf-cookie')
                .then(response => {
                    axios.post('/register', {
                        name: this.name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    })
                    .then( res => {
                        localStorage.setItem('x_xsrf_token', res.config.headers['X-XSRF-TOKEN'])
                        this.$router.push({name: 'postList'})
                    })
                    .catch(err => {
                            console.log(err);
                        })
                })
        }
    }
}
</script>

<style scoped>

</style>
