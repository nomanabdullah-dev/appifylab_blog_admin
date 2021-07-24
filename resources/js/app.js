require('./bootstrap');
import Vue from 'vue'
import router from './router'
import ViewUI from 'view-design'
import 'view-design/dist/styles/iview.css'
import common from './common'
import store from './store'

window.Vue = require('vue').default;

Vue.use(ViewUI);
Vue.component('mainapp', require('./components/mainapp.vue').default)
Vue.mixin(common)

const app = new Vue({
    el: '#app',
    router,
    store
})
