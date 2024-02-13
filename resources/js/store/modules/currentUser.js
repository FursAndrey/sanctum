export default {
    state () {
        return {
            currentUserForSite: {'name': null,'roles': null,'has_ban_chat': null,'has_ban_comment': null},
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