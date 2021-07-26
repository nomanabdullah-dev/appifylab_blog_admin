<template>
    <div class="content">
        <div class="container-fluid">
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <p class="_title0">Admins <Button @click="addModal=true"> <Icon type="md-add" /> Add Admin</Button></p>

                <div class="_overflow _table_div">
                    <table class="_table">
                        <!-- TABLE TITLE -->
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                        <!-- TABLE TITLE -->


                        <!-- ITEMS -->
                        <tr v-for="(user, i) in users" :key="i" v-if="users.length">
                            <td>{{ user.id }}</td>
                            <td class="_table_name">{{ user.fullName }}</td>
                            <td>{{ user.email }}</td>
                            <td>{{ user.role_id }}</td>
                            <td>{{ user.created_at }}</td>
                            <td>
                                <Button type="info" size="small" @click="showEditModal(user, i)">Edit</Button>
                                <Button type="error" size="small" @click="showDeletingModal(user, i)" :loading="user.isDeleting">Delete</Button>
                            </td>
                        </tr>
                        <!-- ITEMS -->


                    </table>
                </div>
            </div>

            <!-- admin adding modal -->
            <Modal
                v-model="addModal"
                title="Add Admin"
                :mask-closable="false"
                :closable="false">
                <Input type="text" v-model="data.fullName" placeholder="Enter full name" style="margin-bottom:7px"/>
                <Input type="email" v-model="data.email" placeholder="Enter email" style="margin-bottom:7px"/>
                <Input type="password" v-model="data.password" placeholder="Enter password" style="margin-bottom:7px"/>
                <Select v-model="data.role_id" placeholder="Select Admin Type">
                    <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                </Select>
                <div slot="footer">
                    <Button type="default" @click="addModal=false">Close</Button>
                    <Button type="primary" @click="addAdmin" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add Admin'}}</Button>
                </div>
            </Modal>

            <!-- admin editing modal -->
            <Modal
                v-model="editModal"
                title="Edit Admin"
                :mask-closable="false"
                :closable="false">
                <Input type="text" v-model="editData.fullName" placeholder="Enter full name" style="margin-bottom:7px"/>
                <Input type="email" v-model="editData.email" placeholder="Enter email" style="margin-bottom:7px"/>
                <Input type="password" v-model="editData.password" placeholder="Enter password" style="margin-bottom:7px"/>
                <Select v-model="editData.role_id" placeholder="Select Admin Type">
                    <Option :value="r.id" v-for="(r, i) in roles" :key="i" v-if="roles.length">{{r.roleName}}</Option>
                </Select>
                <div slot="footer">
                    <Button type="default" @click="editModal=false">Close</Button>
                    <Button type="primary" @click="editAdmin" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Updating..' : 'Update Admin'}}</Button>
                </div>
            </Modal>

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
                data: {
                    fullName : '',
                    email : '',
                    password : '',
                    role_id : null
                },
                addModal: false,
                editModal: false,
                isAdding: false,
                users: [],
                roles : [],
                editData: {
                    fullName : '',
                    email : '',
                    password : '',
                    role_id : null
                },
                index: -1,
                showDeleteModal: false,
                isDeleting: false,
                deleteItem: {},
                deletingIndex: -1,
            }
        },
        methods:{
            async addAdmin(){
                if(this.data.fullName.trim()=='') return this.e('Full name is required')
                if(this.data.email.trim()=='') return this.e('Email is required')
                if(this.data.password.trim()=='') return this.e('Password is required')
                if(!this.data.role_id) return this.e('User type is required')

                const res = await this.callApi('post', 'app/create_user', this.data)
                if (res.status===201){
                    this.users.unshift(res.data)
                    this.s('Admin user has been added successfully!')
                    this.addModal = false
                    this.data.fullName = ''
                    this.data.email = ''
                    this.data.password = ''
                    this.data.userType = ''
                }else{
                    if(res.status==422){
                        for(let i in res.data.errors){
                            this.e(res.data.errors[i][0])
                        }
                    }else{
                        this.swr()
                    }
                }
            },
            showEditModal(user, index){
                let obj = {
                    id : user.id,
                    fullName : user.fullName,
                    email : user.email,
                    userType : user.userType
                }
                this.editData = obj
                this.editModal= true
                this.index = index
            },
            async editAdmin(){
                if(this.editData.fullName.trim()=='') return this.e('Full name is required')
                if(this.editData.email.trim()=='') return this.e('Email is required')
                if(!this.editData.role_id) return this.e('User type is required')

                const res = await this.callApi('post', 'app/edit_user', this.editData)
                if (res.status===200){
                    this.users[this.index] = this.editData
                    this.s('Admin user has been updated successfully!')
                    this.editModal = false
                }else{
                    if(res.status==422){
                        for(let i in res.data.errors){
                            this.e(res.data.errors[i][0])
                        }
                    }else{
                        this.swr()
                    }
                }
            },
            showDeletingModal(user, i){
                const deleteModalObj = {
                    showDeleteModal : true,
                    deleteUrl : 'app/delete_user',
                    data : user,
                    deletingIndex: i,
                    isDeleted : false,
                }
                this.$store.commit('setDeletingModalObj', deleteModalObj)
            },
        },
        async created(){
            const [res, resRole] = await Promise.all([
                this.callApi('get', 'app/get_users'),
                this.callApi('get', 'app/get_roles')
            ]);
            if(res.status == 200){
                this.users = res.data
            }else{
                this.swr()
            }
            if(resRole.status == 200){
                this.roles = resRole.data
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
                    this.users.splice(obj.deletingIndex,1)
                }
            }
        }
    }

</script>

<style scoped>

</style>
