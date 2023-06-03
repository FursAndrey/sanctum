import { ref } from "vue";

export default function usePagination() {
    const page = ref(1);

    const goToFirstPage = () => {
        page.value = 1;
    }

    const goToLastPage = (last_page) => {
        page.value = last_page;
    }

    const goToNextPage = (last_page) => {
        if (page.value < last_page) {
            page.value += 1;
        }
    }

    const goToPreviusPage = () => {
        if (1 < page.value) {
            page.value -= 1;
        }
    }

    return {
        page,
        goToFirstPage,
        goToPreviusPage,
        goToNextPage,
        goToLastPage,
    }
}