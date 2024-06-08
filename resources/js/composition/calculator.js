import { ref, reactive } from "vue";
import axios from "axios";

export default function useCalculator() {
    const total = reactive([]);
    const items = reactive([]);
    const errorMessage = ref('');
    
    const getItems = async () => {}

    const addItem = async (newItem) => {
        if (newItem.type == 1) {
            items.push({
                num: newItem.num, 
                p: newItem.p.replace(',', '.'),
                cos: newItem.cos.replace(',', '.'),
                pv: 1,
                type: newItem.type
            });
        } else if (newItem.type == 2) {
            items.push({
                num: newItem.num, 
                p: newItem.p.replace(',', '.'),
                cos: 1,
                pv: 1,
                type: newItem.type
            });
        } else {
            items.push({
                num: newItem.num, 
                p: newItem.p.replace(',', '.'),
                cos: 1,
                pv: newItem.pv.replace(',', '.'),
                type: newItem.type
            });
        }

        await sendForCalc();

        if (errorMessage.value == '') {
            return true;
        } else {
            items.pop();
            total.value.hasError = false;
            return false;
        }
    }

    const removeItem = async (item) => {
        for (let index = 0; index < items.length; index++) {
            if (items[index].num === item.num) {
                items.splice(index, 1);
            }
        }

        await sendForCalc();
    }

    const editItem = async (oldItemNum, newItem) => {
        for (let index = 0; index < items.length; index++) {
            if (items[index].num === oldItemNum) {
                if (newItem.type == 1) {
                    items[index].num = newItem.num;
                    items[index].p = newItem.p.replace(',', '.');
                    items[index].cos = newItem.cos.replace(',', '.');
                    items[index].pv = 1;
                    items[index].type = newItem.type;
                } else if (newItem.type == 2) {
                    items[index].num = newItem.num;
                    items[index].p = newItem.p.replace(',', '.');
                    items[index].cos = 1;
                    items[index].pv = 1;
                    items[index].type = newItem.type;
                } else {
                    items[index].num = newItem.num;
                    items[index].p = newItem.p.replace(',', '.');
                    items[index].cos = 1;
                    items[index].pv = newItem.pv.replace(',', '.');
                    items[index].type = newItem.type;
                }
            }
        }

        await sendForCalc();

        if (errorMessage.value == '') {
            return true;
        } else {
            return false;
        }
    }

    const sendForCalc = async () => {
        errorMessage.value = '';

        try {
            await axios.post('/api/calculator', {items: items})
                .then(res => {
                    total.value = [];
                    for (let index = 0; index < items.length; index++) {
                        items[index].num = res.data.items[index].num;
                        items[index].p = res.data.items[index].p;
                        items[index].i = res.data.items[index].i;
                        items[index].cos = res.data.items[index].cos;
                        items[index].pv = res.data.items[index].pv;
                        items[index].type = res.data.items[index].type;
                        items[index].breakerNominal = res.data.items[index].breakerNominal;
                    }
                    total.value = res.data.total;
                });
        } catch(e) {
            let erObj = {
                num: '',
                p: '',
                cos: '',
                pv: '',
                type: '',
            };
            for (const key in e.response.data.errors) {
                let tmp = key.split('.')[2];
                
                erObj[tmp] = e.response.data.errors[key][0].replace(key, tmp);
            }
            errorMessage.value = erObj;
            total.value.hasError = true;
        }
    }

    const resetErrors = () => {
        errorMessage.value = '';
    }

    return {
        total,
        items,
        errorMessage,
        getItems,
        addItem,
        removeItem,
        editItem,
        resetErrors
    }
}