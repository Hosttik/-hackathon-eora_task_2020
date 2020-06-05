// src/plugins/vuetify.js

import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
import store from 'src/store/store';
import colors from 'vuetify/es5/util/colors';

Vue.use(Vuetify)

const opts = {
  icons: {
    iconfont: 'fa',
  },
  theme: {
    themes: {
      light: {
        primary: colors[store.state.theme].lighten1,
        secondary: colors[store.state.theme].darken1,
        accent: colors[store.state.theme].accent1
      }
    }
  }
};

export default new Vuetify(opts)
