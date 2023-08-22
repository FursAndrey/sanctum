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