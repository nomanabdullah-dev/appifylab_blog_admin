require('./bootstrap');
import Vue from 'vue'
import router from './router'
import ViewUI from 'view-design'
import 'view-design/dist/styles/iview.css'
import common from './common'
import jsonToHtml from './jsonToHtml'
import store from './store'
import Editor from 'vue-editor-js'


window.Vue = require('vue').default;

Vue.use(Editor)
Vue.use(ViewUI);
Vue.component('mainapp', require('./components/mainapp.vue').default)
Vue.mixin(common)
Vue.mixin(jsonToHtml)

const app = new Vue({
    el: '#app',
    router,
    store
})
