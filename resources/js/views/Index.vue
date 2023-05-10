<template>
    <div>
        Index
    </div>
</template>

<script>
export default {
    name: 'Index',
    
    mounted() {
        this.getCloseData();
        this.getOpenData();
    },

    methods: {
        getOpenData() {
            axios.get('/api/open')
                .then(res => {
                    console.log(res);
                })
                .catch(err => {
                    console.log(err);
                })
        },

        getCloseData() {
            axios.get('/api/close')
                .then(res => {
                    console.log(res);
                })
                .catch(err => {
                    console.log(err);
                })
        }
    }
}
</script>

<style scoped>

</style>
