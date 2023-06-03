<template>
    <div>
        <div v-if="meta" class="pt-4 pb-6">Posts form: {{ meta.from }} to: {{ meta.to }}. Total posts: {{ meta.total }}.</div>
        <div v-if="meta" class="pb-2 mb-8">
            <span 
                @click="firstPage()" 
                class="w-16 p-2 me-4 rounded-lg text-center cursor-pointer" 
                :class="{
                    'bg-amber-600': meta.current_page != 1,
                    'hover:bg-amber-800': meta.current_page != 1,
                    'bg-neutral-600': meta.current_page == 1,
                    'cursor-pointer': meta.current_page != 1,
                }"
                >
                first
            </span>
            <span 
                @click="previusPage()" 
                class="w-16 p-2 me-4 rounded-lg text-center"
                :class="{
                    'bg-amber-600': meta.current_page != 1,
                    'hover:bg-amber-800': meta.current_page != 1,
                    'bg-neutral-600': meta.current_page == 1,
                    'cursor-pointer': meta.current_page != 1,
                }"
                >
                previus
            </span>
            <span 
                @click="nextPage()" 
                class="w-16 p-2 me-4 rounded-lg text-center"
                :class="{
                    'bg-amber-600': meta.current_page != meta.last_page,
                    'hover:bg-amber-800': meta.current_page != meta.last_page,
                    'bg-neutral-600': meta.current_page == meta.last_page,
                    'cursor-pointer': meta.current_page != meta.last_page,
                }"
                >
                next
            </span>
            <span 
                @click="lastPage()" 
                class="w-16 p-2 rounded-lg text-center"
                :class="{
                    'bg-amber-600': meta.current_page != meta.last_page,
                    'hover:bg-amber-800': meta.current_page != meta.last_page,
                    'bg-neutral-600': meta.current_page == meta.last_page,
                    'cursor-pointer': meta.current_page != meta.last_page,
                }"
                >
                last
            </span>
        </div>
    </div>
</template>

<script>
import usePagination from '../composition/pagination.js';
export default {
    name: 'paginationTemplate',
    props: {
        meta: {
            type: Object,
            required: true
        }
    },
    
    setup(props, {emit}) {
        const { page, goToFirstPage, goToNextPage, goToPreviusPage, goToLastPage } = usePagination();

        const firstPage = async () => {
            goToFirstPage();
            emit('changePage', page.value);
        }

        const lastPage = async () => {
            goToLastPage(props.meta.last_page);
            emit('changePage', page.value);
        }

        const nextPage = async () => {
            goToNextPage(props.meta.last_page);
            emit('changePage', page.value);
        }

        const previusPage = async () => {
            goToPreviusPage();
            emit('changePage', page.value);
        }

        return {
            firstPage,
            lastPage,
            nextPage,
            previusPage
        }
    },
}
</script>

<style>

</style>