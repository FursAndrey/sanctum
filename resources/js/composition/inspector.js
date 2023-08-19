import { ref } from "vue";

export default function useInspector() {

    const isAdmin = (roles) => {
        if (roles == undefined) {
            return false;
        }
        if (roles.includes("Admin")) {
            return true;
        } else {
            return false;
        }
    }

    return {
        isAdmin,
    }
}