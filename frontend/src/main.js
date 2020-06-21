import Vue from 'vue'
import App from '@/App'
import router from '@/router'

Vue.config.productionTip = false

// Global SASS Own Styles
import 'vuetify/dist/vuetify.min.css'
import '@/styles/main.scss'

// Package Install
import Vuetify from 'vuetify';
import axios from 'axios'
import VueAxios from 'vue-axios'

Vue.use(Vuetify)
Vue.use(VueAxios, axios);

// API Config
import ApiConfig from '@/config'

Vue.use(ApiConfig);

new Vue({
  router, 
  vuetify : new Vuetify(),
  render: h => h(App)
}).$mount('#app')
