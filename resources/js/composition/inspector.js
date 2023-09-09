import { ref } from "vue";

import { computed } from 'vue'
import { useStore } from 'vuex'

export default function useInspector() {
    const store = useStore();
    const myRoles = computed(() => store.getters.rolesCurrentUser);

    const isAdmin = () => {
        if (myRoles.value == undefined) {
            return false;
        }
        if (myRoles.value.includes("Admin")) {
            return true;
        } else {
            return false;
        }
    }
    
    const isAuth = () => {
        let token = localStorage.getItem('x_xsrf_token');
        if (token === null) {
            return false;
        } else {
            return true;
        }
    }

    return {
        isAdmin,
        isAuth
    }
}