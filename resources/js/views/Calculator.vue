<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Калькулятор</h1>
        <div class="p-4 my-4 border border-indigo-600 shadow-md shadow-indigo-800">
            Результаты расчета:
            <div v-if="total.value">
                <span>Количество элементов: {{ total.value.count }}шт</span>&nbsp;
                <span>Ssum: {{ total.value.Psum }}кВА</span>&nbsp;
                <span>Isum: {{ total.value.Isum }}A</span>
                <div v-if="total.value.hasError == true" class="my-2 p-4 bg-red-800 rounded-lg">
                    При добавлении / редактировании элемента произошла ошибка. Результат расчета может быть недостоверным, отредактируйте или удалите некорректные элементы.
                </div>
            </div>
        </div>
        <div class="flex justify-between flex-wrap">
            <div
                v-for="item in items" 
                :key="item.num" 
                class="flex flex-col mx-auto w-64 m-4 p-4 text-justify border border-indigo-600 shadow-md shadow-indigo-800">
                <div v-if="item.num == '' || item.p == '' || item.i == '' || item.cos == '' || item.pv == '' || item.type == ''" class="my-2 p-4 bg-red-800 rounded-lg">
                    Элемент заполнен не корректно, отредактируйте его или удалите.
                </div>
                <div class="flex-1 mb-4">№{{ item.num }}</div>
                <div class="flex-1 mb-4">Мощность: {{ item.p }}<span v-if="item.type == 3">кВА</span><span v-else>кВт</span></div>
                <div class="flex-1 mb-4">Сила тока: {{ item.i }}A</div>
                <div class="flex-1 mb-4" v-if="item.type == 1">cos: {{ item.cos }}</div>
                <div class="flex-1 mb-4" v-if="item.type == 3">Продолжительность включения: {{ item.pv }}</div>
                <div class="flex-1 mb-4">Тип: {{ showTypeName(item.type) }}</div>
                <div class="flex justify-between">
                    <div class="edit-btn w-2/5 block p-3 text-center rounded-lg bg-indigo-800 cursor-pointer" @click="openChangeModal(item)"></div>
                    <div class="delete-btn w-2/5 block p-3 text-center rounded-lg bg-indigo-800 cursor-pointer" @click="deleteItem(item)"></div>
                </div>
            </div>
            <div
                class="flex mx-auto w-64 m-4 p-4 text-justify border border-indigo-600 shadow-md shadow-indigo-800 cursor-pointer">
                <div class="mb-4 flex items-center text-center text-3xl" @click="openModal()">Добавить новый элемент</div>
            </div>
        </div>
        <div class="modal flex items-center" v-if="showModal === true">
            <div class="modal-main mx-auto rounded-lg p-16">
                <h2 class="text-3xl">Добавление / редактирование элементов</h2>
                <div>
                    <p>Номер</p>
                    <input type="text" v-model="newItem.num" placeholder="Номер" class="w-96 p-2 my-6 border border-inherit rounded-lg">
                    <p v-if="errorMessage.num != undefined && errorMessage.num != ''" class="my-2 p-2 bg-red-500 rounded-lg">{{ errorMessage.num }}</p>
                </div>
                <div>
                    <p>Мощность</p>
                    <input type="text" v-model="newItem.p" placeholder="Мощность" class="w-96 p-2 mb-6 border border-inherit rounded-lg">
                    <p v-if="errorMessage.p != undefined && errorMessage.p != ''" class="my-2 p-2 bg-red-500 rounded-lg">{{ errorMessage.p }}</p>
                </div>
                <div v-if="newItem.type == 1">
                    <p>cos</p>
                    <input type="text" v-model="newItem.cos" placeholder="cos" class="w-96 p-2 mb-6 border border-inherit rounded-lg">
                    <p v-if="errorMessage.cos != undefined && errorMessage.cos != ''" class="my-2 p-2 bg-red-500 rounded-lg">{{ errorMessage.cos }}</p>
                </div>
                <div v-if="newItem.type == 3">
                    <p>Продолжительность включения</p>
                    <input type="text" v-model="newItem.pv" placeholder="Продолжительность включения" class="w-96 p-2 mb-6 border border-inherit rounded-lg">
                    <p v-if="errorMessage.pv != undefined && errorMessage.pv != ''" class="my-2 p-2 bg-red-500 rounded-lg">{{ errorMessage.pv }}</p>
                </div>
                <div>
                    <p>Выберите тип опорудования:</p>
                    <select v-model="newItem.type" class="text-gray-950 mb-6">
                        <option v-for="type1 in types" :key="type1.value" :value="type1.value">
                            {{ type1.text }}
                        </option>
                    </select>
                    <p v-if="errorMessage.type != undefined && errorMessage.type != ''" class="my-2 p-2 bg-red-500 rounded-lg">{{ errorMessage.type }}</p>
                </div>
                <div class="flex">
                    <span v-if="editModal === false" @click="addNewItem()" class="p-2 me-6 font-bold bg-green-700 text-white rounded-lg text-center cursor-pointer">Добавить</span>
                    <span v-else @click="changeOldItem()" class="p-2 me-6 font-bold bg-green-700 text-white rounded-lg text-center cursor-pointer">Изменить</span>
                    <span @click="closeModal()" class="p-2 me-6 font-bold bg-orange-600 text-white rounded-lg text-center cursor-pointer">Отмена</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue';
import useCalculator from '../composition/calculator';
export default {
    name: 'calculator',
    
    setup() {
        const newItem = reactive({});
        const oldItemForChange = reactive({
            'num': '',
            'p': '',
            'cos': '',
            'pv': '',
            'type': '',
        });
        const types = ref([
            { text: 'Металлорежущие станки', value: '1' },
            { text: 'Электрические печи', value: '2' },
            { text: 'Сварочные установки', value: '3' },
        ]);
        const { items, total, errorMessage, getItems, addItem, removeItem, editItem, resetErrors } = useCalculator();
        const showModal = ref(false);
        const editModal = ref(false);
        
        onMounted(getItems());

        /** открыть модальное окно для добавления нового элемента */
        const openModal = () => {
            resetErrors();
            showModal.value = true;
            editModal.value = false;
            oldItemForChange.num = '';
            
            newItem.num = '';
            newItem.p = '';
            newItem.cos = '';
            newItem.pv = '';
            newItem.type = '';
        }

        const closeModal = () => {
            showModal.value = false;
        }

        const addNewItem = async () => {
            let check = await addItem({...newItem});

            if (check === true) {
                showModal.value = false;
                newItem.num = '';
                newItem.p = '';
                newItem.cos = '';
                newItem.pv = '';
                newItem.type = '';
            }
        }

        const deleteItem = (item) => {
            removeItem(item);
        }

        /** открыть модальное окно для редактирования элемента */
        const openChangeModal = (item) => {
            resetErrors();
            showModal.value = true;
            editModal.value = true;
            newItem.num = item.num;
            newItem.p = item.p;
            newItem.cos = item.cos;
            newItem.pv = item.pv;
            newItem.type = item.type;

            oldItemForChange.num = item.num;
            oldItemForChange.p = item.p;
            oldItemForChange.cos = item.cos;
            oldItemForChange.pv = item.pv;
            oldItemForChange.type = item.type;
        }

        const changeOldItem = async () => {
            let check = await editItem(oldItemForChange.num, {...newItem});
            
            if (check === true) {
                oldItemForChange.num = '';
                showModal.value = false;
                newItem.num = '';
                newItem.p = '';
                newItem.cos = '';
                newItem.pv = '';
                newItem.type = '';
            }
        }

        const showTypeName = (typeCode) => {
            for (const code in types.value) {
                if (types.value[code].value == typeCode) {
                    return types.value[code].text;
                }
            }
            return typeCode;
        }

        return {
            types,
            total,
            items,
            showModal,
            editModal,
            newItem,
            errorMessage,
            openModal,
            closeModal,
            addNewItem,
            deleteItem,
            openChangeModal,
            changeOldItem,
            showTypeName
        }
    }
}
</script>

<style scoped>
div.edit-btn {
    height: 50px;
    background-size: 30px;
    background-repeat: no-repeat;
    background-position: center;
    background-image: url('/storage/app/public/theme/pencil.png');
}
div.delete-btn {
    height: 50px;
    background-size: 30px;
    background-repeat: no-repeat;
    background-position: center;
    background-image: url('/storage/app/public/theme/trash.png');
}
.modal {
    background: rgba(0, 0, 0, 0.9);
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}
.modal > .modal-main {
    background-color: rgb(35, 0, 100);
    width: 50%;
    /* height: 45%; */
}
</style>