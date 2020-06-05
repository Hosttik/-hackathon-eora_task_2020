import Vue from 'vue';
import Vuex from 'vuex';
import actions from './actions';
import mutations from './mutations';
import getters from './getters';

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
    theme: 'indigo',
    loader: false,
    isAuthorized:true,
    userData: {},
    currentAdvert: null,
    messageList: []
  },
  actions,
  mutations,
  getters
});

export default store;
