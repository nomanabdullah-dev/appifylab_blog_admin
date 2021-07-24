import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

//admin project pages
import home from './components/pages/home.vue'
import tags from './admin/pages/tags.vue'
import category from './admin/pages/category.vue'

const routes = [
    {
        path: '/',
        component: home,
        name: 'home'
    },
    {
        path: '/tags',
        component: tags,
        name: 'tags'
    },
    {
        path: '/category',
        component: category,
        name: 'category'
    }
]

export default new Router({
    mode: 'history',
    routes
})
