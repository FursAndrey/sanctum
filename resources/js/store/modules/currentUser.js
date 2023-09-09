export default {
    state () {
        return {
            currentUserForSite: {'name': null,'roles': null},
        }
    },
    mutations: {
        updateCurrentUserForSite(state, user) {
            state.currentUserForSite = user;
        }
    },
    actions: {
        async fetchCurrentUserForSite(context) {
            await axios.get('/api/currentUser')
                .then( res => {
                    context.commit('updateCurrentUserForSite', res.data.data);
                });
        }
    },
    getters: {
        currentUserForSite(state) {
            return state.currentUserForSite;
        },
        rolesCurrentUser(state) {
            return state.currentUserForSite.roles;
        }
    },
}