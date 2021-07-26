<template>
    <div class="content">
        <div class="container-fluid">
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <p class="_title0">Blogs <Button @click="$router.push('createBlog')" v-if="isWritePermitted"> <Icon type="md-add" /> Create Tag</Button></p>

                <div class="_overflow _table_div">
                    <table class="_table">
                        <!-- TABLE TITLE -->
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Categories</th>
                            <th>Tags</th>
                            <th>Views</th>
                            <th>Action</th>
                        </tr>
                        <!-- TABLE TITLE -->


                        <!-- ITEMS -->
                        <tr v-for="(blog, i) in blogs" :key="i" v-if="blogs.length">
                            <td>{{ blog.id }}</td>
                            <td class="_table_name">{{ blog.title }}</td>
                            <td><span v-for="(c, j) in blog.cat" :key="j" v-if="blog.cat.length"><Tag type="border">{{ c.categoryName }}</Tag></span></td>
                            <td><span v-for="(t, k) in blog.tag" :key="k" v-if="blog.tag.length"><Tag type="border">{{ t.tagName }}</Tag></span></td>
                            <td>{{ blog.views }}</td>
                            <td>
                                <Button type="info" size="small" @click="" v-if="isUpdatePermitted">Visit Blog</Button>
                                <Button type="info" size="small" @click="showEditModal(blog, i)" v-if="isUpdatePermitted">Edit</Button>
                                <Button type="error" size="small" @click="showDeletingModal(blog, i)" :loading="blog.isDeleting" v-if="isDeletePermitted">Delete</Button>
                            </td>
                        </tr>
                        <!-- ITEMS -->


                    </table>
                </div>
            </div>

            <deleteModal></deleteModal>
        </div>
    </div>
</template>

<script>
import { mapGetters } from 'vuex'
import deleteModal from '../components/deleteModal.vue'
    export default {
        data(){
            return{

                isAdding: false,
                blogs: [],
                index: -1,
                showDeleteModal: false,
                isDeleting: false,
                deleteItem: {},
                deletingIndex: -1
            }
        },
        methods:{
            showDeletingModal(blog, i){
                const deleteModalObj = {
                    showDeleteModal : true,
                    deleteUrl : 'app/delete_blog',
                    data : {id: blog.id},
                    deletingIndex: i,
                    isDeleted : false,
                }
                this.$store.commit('setDeletingModalObj', deleteModalObj)
            },
        },
        async created(){
            console.log(this.isWritePermitted)
            const res = await this.callApi('get', 'app/blogsdata')
            if(res.status == 200){
                this.blogs = res.data
            }else{
                this.swr()
            }
        },
        components : {
            deleteModal
        },
        computed : {
            ...mapGetters(['getDeleteModalObj'])
        },
        watch : {
            getDeleteModalObj(obj){
                console.log(obj)
                if(obj.isDeleted){
                    this.blogs.splice(obj.deletingIndex,1)
                }
            }
        }
    }

</script>

<style scoped>

</style>
