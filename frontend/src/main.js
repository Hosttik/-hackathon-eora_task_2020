import Vue from "vue";
import App from "src/components/App/App";
import router from "src/router/router";
import store from "src/store/store";
import Notifications from 'vue-notification';
import vuetify from 'src/plugins/vuetify';

Vue.use(Notifications);

new Vue({
  el: "#app",
  router,
  store,
  vuetify,
  template: "<App/>",
  components: { App }
});
