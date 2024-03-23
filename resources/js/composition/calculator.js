import { ref, reactive } from "vue";
// import axios from "axios";
// import { useRouter } from "vue-router";

export default function useCalculator() {
    // const item = ref([]);
    const items = reactive([]);
    // const errorMessage = ref('');
    // const router = useRouter();
    
    // const getChat = async (id) => {
    //     let response = await axios.get('/api/items/' + id);
    //     item.value = response.data;
    // }

    const getItems = async () => {}

    const addItem = async (newItem) => {
        let i = newItem.p / 2;
        items.push({num: newItem.num, p: newItem.p, i: i});
    }

    const removeItem = async (item) => {
        for (let index = 0; index < items.length; index++) {
            if (items[index].num === item.num) {
                items.splice(index, 1);
            }
        }
    }

    const editItem = async (oldItemNum, newItem) => {
        for (let index = 0; index < items.length; index++) {
            if (items[index].num === oldItemNum) {
                items[index].num = newItem.num;
                items[index].p = newItem.p;
                items[index].i = newItem.p / 2;
            }
        }
    }

    // const storeChat = async (data) => {
    //     errorMessage.value = '';

    //     try {
    //         await axios.post('/api/items', data)
    //         .then(item => {
    //             router.push({ name: 'items.show', params:{ id: String(item.data.id) } });
    //         });
    //     } catch(e) {
    //         errorMessage.value = e.response.data.message;
    //     }
    // }

    // const updateChat = async (id) => {
    //     errorMessage.value = '';
        
    //     try {
    //         await axios.put('/api/items/' + id, {title: item.value.title, discription: item.value.discription});
    //         await router.push({ name: 'item.index' });
    //     } catch(e) {
    //         errorMessage.value = e.response.data.message;
    //     }
    // }
    
    // const destroyChat = async (id) => {
    //     await axios.delete('/api/items/'+id);
    //     await router.push({ name: 'items' });
    // }
    
    return {
        // item,
        items,
        // errorMessage,
        // getChat,
        getItems,
        addItem,
        removeItem,
        editItem
        // storeChat,
        // updateChat,
        // destroyChat
    }
}