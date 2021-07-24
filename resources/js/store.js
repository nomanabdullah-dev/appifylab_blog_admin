import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)


const store = new Vuex.Store({
    state: {
        deleteModalObj : {
            showDeleteModal : false,
            deleteUrl : '',
            data : null,
            deletingIndex: -1,
            isDeleted : false,
        }
    },
    getters : {
        getDeleteModalObj(state){
            return state.deleteModalObj
        }
    },
    mutations: {
        setDeleteModal(state, data){
            const deleteModalObj = {
                showDeleteModal : false,
                deleteUrl : '',
                data : null,
                deletingIndex: -1,
                isDeleted : data,
            }
            state.deleteModalObj = deleteModalObj
        },
        setDeletingModalObj(state, data){
            state.deleteModalObj = data
        }
    }
})

export default store;
