import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

//project pages
import home from './components/pages/home.vue'
import tags from './admin/pages/tags.vue'
import category from './admin/pages/category.vue'
import createBlog from './admin/pages/createBlog.vue'
import blogs from './admin/pages/blogs.vue'
// admin user
import adminusers from './admin/pages/adminusers.vue'
//login
import login from './admin/pages/login.vue'
//role
import role from './admin/pages/role.vue'
import assignRole from './admin/pages/assignRole.vue'


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
    },
    {
        path: '/createBlog',
        component: createBlog,
        name: 'createBlog'
    },
    {
        path: '/blogs',
        component: blogs,
        name: 'blogs'
    },
    //admin users
    {
        path: '/adminusers',
        component: adminusers,
        name: 'adminusers'
    },
    //login
    {
        path: '/login',
        component: login,
        name: 'login'
    },
    //role
    {
        path: '/role',
        component: role,
        name: 'role'
    },
    {
        path: '/assignRole',
        component: assignRole,
        name: 'assignRole'
    },
]

export default new Router({
    mode: 'history',
    routes
})
