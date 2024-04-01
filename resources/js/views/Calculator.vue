<template>
    <div>
        <h1 class="text-3xl font-bold text-center">Калькулятор</h1>
        <div class="p-4 my-4 border border-indigo-600 shadow-md shadow-indigo-800">
            Результаты расчета:
            <div v-if="total.value">
                <span>Количество элементов: {{ total.value.count }}шт</span>&nbsp;
                <span>Psum: {{ total.value.Psum }}кВт</span>&nbsp;
                <span>Isum: {{ total.value.Isum }}А</span>
            </div>
        </div>
        <div class="flex justify-between flex-wrap">
            <div
                v-for="item in items" 
                :key="item.num" 
                class="flex flex-col mx-auto w-64 m-4 p-4 text-justify border border-indigo-600 shadow-md shadow-indigo-800">
                <div class="flex-1 mb-4">№{{ item.num }}</div>
                <div class="flex-1 mb-4">Мощность {{ item.p }}кВт</div>
                <div class="flex-1 mb-4">Сила тока {{ item.i }}А</div>
                <div class="flex-1 mb-4">cos {{ item.cos }}</div>
                <div class="flex-1 mb-4">КПД {{ item.kpd }}</div>
                <div class="flex-1 mb-4">Тип {{ item.type }}</div>
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
                <p v-if="error != ''" class="my-2 p-2 bg-red-500 rounded-lg">{{ error }}</p>
                <div>
                    <input type="text" v-model="newItem.num" placeholder="Номер" class="w-96 p-2 my-6 border border-inherit rounded-lg">
                </div>
                <div>
                    <input type="text" v-model="newItem.p" placeholder="Мощность" class="w-96 p-2 mb-6 border border-inherit rounded-lg">
                </div>
                <div>
                    <input type="text" v-model="newItem.cos" placeholder="cos" class="w-96 p-2 mb-6 border border-inherit rounded-lg">
                </div>
                <div>
                    <input type="text" v-model="newItem.kpd" placeholder="КПД" class="w-96 p-2 mb-6 border border-inherit rounded-lg">
                </div>
                <div>
                    <p>Выберите тип опорудования:</p>
                    <select v-model="newItem.type" class="text-gray-950 mb-6">
                        <option v-for="type1 in types" :key="type1.value" :value="type1.value">
                            {{ type1.text }}
                        </option>
                    </select>
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
        const newItem = reactive({
            'num': '',
        });
        const oldItemForChange = reactive({
            'num': '',
            'p': '',
            'cos': '',
            'kpd': '',
            'type': '',
        });
        const types = ref([
            { text: 'Металлорежущие станки', value: '1' },
            { text: 'Печи, сварочные установки', value: '2' },
        ]);
        const { items, total, getItems, addItem, removeItem, editItem } = useCalculator();
        const showModal = ref(false);
        const error = ref('');
        const editModal = ref(false);
        
        onMounted(getItems());

        const openModal = () => {
            showModal.value = true;
            editModal.value = false;
            oldItemForChange.num = '';
        }

        const closeModal = () => {
            showModal.value = false;
        }

        const addNewItem = () => {
            addItem({...newItem});
            showModal.value = false;
            newItem.num = '';
            newItem.p = '';
            newItem.cos = '';
            newItem.kpd = '';
            newItem.type = '';
        }

        const deleteItem = (item) => {
            removeItem(item);
        }

        const openChangeModal = (item) => {
            showModal.value = true;
            editModal.value = true;
            newItem.num = item.num;
            newItem.p = item.p;
            newItem.cos = item.cos;
            newItem.kpd = item.kpd;
            newItem.type = item.type;
            oldItemForChange.num = item.num;
        }

        const changeOldItem = () => {
            editItem(oldItemForChange.num, {...newItem});
            oldItemForChange.num = '';
            showModal.value = false;
            newItem.num = '';
            newItem.p = '';
            newItem.cos = '';
            newItem.kpd = '';
            newItem.type = '';
        }

        return {
            error,
            types,
            total,
            items,
            showModal,
            editModal,
            newItem,
            openModal,
            closeModal,
            addNewItem,
            deleteItem,
            openChangeModal,
            changeOldItem
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
    background: rgba(0, 0, 0, 0.8);
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