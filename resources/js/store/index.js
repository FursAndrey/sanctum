import { createStore } from 'vuex';
import currentUser from './modules/currentUser';

export default createStore({
    modules: {
        currentUser
    }
})