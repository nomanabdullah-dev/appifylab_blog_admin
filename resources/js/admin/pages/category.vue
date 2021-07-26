<template>
    <div class="content">
        <div class="container-fluid">
            <!--~~~~~~~ TABLE ONE ~~~~~~~~~-->
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20">
                <p class="_title0">Categories <Button @click="addModal=true" v-if="isWritePermitted"> <Icon type="md-add" /> Add Category</Button></p>

                <div class="_overflow _table_div">
                    <table class="_table">
                        <!-- TABLE TITLE -->
                        <tr>
                            <th>ID</th>
                            <th>Icon Image</th>
                            <th>Category name</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                        <!-- TABLE TITLE -->


                        <!-- ITEMS -->
                        <tr v-for="(category, i) in CategoryLists" :key="i" v-if="CategoryLists.length">
                            <td>{{ category.id }}</td>
                            <td class="table_image">
                                <img :src="category.iconImage"/>
                            </td>
                            <td class="_table_name">{{ category.categoryName }}</td>
                            <td>{{ category.created_at }}</td>
                            <td>
                                <Button type="info" size="small" @click="showEditModal(category, i)" v-if="isUpdatePermitted">Edit</Button>
                                <Button type="error" size="small" @click="showDeletingModal(category, i)" :loading="category.isDeleting"  v-if="isDeletePermitted">Delete</Button>
                            </td>
                        </tr>
                        <!-- ITEMS -->


                    </table>
                </div>
            </div>

            <!-- category adding modal -->
            <Modal
                v-model="addModal"
                title="Add Category"
                :mask-closable="false"
                :closable="false">
                <Input
                    v-model="data.categoryName"
                    placeholder="Enter category name"
                />
                <div class="space" style="margin-top:10px">
                    <Upload
                        ref="uploads"
                        type="drag"
                        :headers="{'x-csrf-token' : token, 'X-Requested-With' : 'XMLHttpRequest'}"
                        :on-success="handleSuccess"
                        :on-error="handleError"
                        :format="['jpg','jpeg','png']"
                        :max-size="2048"
                        :on-format-error="handleFormatError"
                        :on-exceeded-size="handleMaxSize"
                        action="/app/upload">
                        <div style="padding: 20px 0">
                            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                            <p>Click or drag files here to upload</p>
                        </div>
                    </Upload>
                    <div class="demo-upload-list" v-if="data.iconImage">
                        <img :src="`${data.iconImage}`">
                        <div class="demo-upload-list-cover">
                            <Icon type="ios-trash-outline" @click="deleteImage"></Icon>
                        </div>
                    </div>
                </div>

                <div slot="footer">
                    <Button type="default" @click="addModal=false">Close</Button>
                    <Button type="primary" @click="addCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Adding..' : 'Add Category'}}</Button>
                </div>
            </Modal>

            <!-- category editing modal -->
            <Modal
                v-model="editModal"
                title="Edit category"
                :mask-closable="false"
                :closable="false">
                <Input
                    v-model="editData.categoryName"
                    placeholder="Enter category name"
                />
                <div class="space" style="margin-top:10px">
                    <Upload v-show="isIconImageNew"
                        ref="editDataUploads"
                        type="drag"
                        :headers="{'x-csrf-token' : token, 'X-Requested-With' : 'XMLHttpRequest'}"
                        :on-success="handleSuccess"
                        :on-error="handleError"
                        :format="['jpg','jpeg','png']"
                        :max-size="2048"
                        :on-format-error="handleFormatError"
                        :on-exceeded-size="handleMaxSize"
                        action="/app/upload">
                        <div style="padding: 20px 0">
                            <Icon type="ios-cloud-upload" size="52" style="color: #3399ff"></Icon>
                            <p>Click or drag files here to upload</p>
                        </div>
                    </Upload>
                    <div class="demo-upload-list" v-if="editData.iconImage">
                        <img :src="`${editData.iconImage}`">
                        <div class="demo-upload-list-cover">
                            <Icon type="ios-trash-outline" @click="deleteImage(false)"></Icon>
                        </div>
                    </div>
                </div>

                <div slot="footer">
                    <Button type="default" @click="closeEditModal">Close</Button>
                    <Button type="primary" @click="editCategory" :disabled="isAdding" :loading="isAdding">{{isAdding ? 'Updating..' : 'Update Category'}}</Button>
                </div>
            </Modal>


            <!-- category deleting modal -->
            <!-- <Modal v-model="showDeleteModal" width="360">
                <p slot="header" style="color:#f60;text-align:center">
                    <Icon type="ios-information-circle"></Icon>
                    <span>Delete confirmation</span>
                </p>
                <div style="text-align:center">
                    <p>Are you sure you want to delete category?</p>
                </div>
                <div slot="footer">
                    <Button type="error" size="large" long :loading="isDeleting" :disabled="isDeleting" @click="deleteCategory">Delete</Button>
                </div>
            </Modal> -->
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
                    categoryName: '',
                    iconImage: ''
                },
                addModal: false,
                editModal: false,
                isAdding: false,
                CategoryLists: [],
                editData: {
                    categoryName: '',
                    iconImage: ''
                },
                index: -1,
                showDeleteModal: false,
                isDeleting: false,
                deleteItem: {},
                deletingIndex: -1,
                token: '',
                isIconImageNew: false,
                isEditingItem: false
            }
        },
        methods:{
            async addCategory(){
                if(this.data.categoryName.trim()=='') return this.e('Category name is required')
                if(this.data.iconImage.trim()=='') return this.e('Icon image is required')
                this.data.iconImage = `${this.data.iconImage}`
                const res = await this.callApi('post', 'app/create_category', this.data)
                if (res.status===201){
                    this.CategoryLists.unshift(res.data)
                    this.s('Category has been added successfully!')
                    this.addModal = false
                    this.data.categoryName = ''
                    this.data.iconImage = ''
                }else{
                    if(res.status==422){
                        if(res.data.errors.categoryName){
                            this.e(res.data.errors.categoryName[0])
                        }
                        if(res.data.errors.iconImage){
                            this.e(res.data.errors.iconImage[0])
                        }
                    }else{
                        this.swr()
                    }
                }
            },
            showEditModal(category, index){
                // let obj = {
                //     id : tag.id,
                //     tagName : tag.tagName
                // }
                this.editData = category
                this.editModal= true
                this.index = index
                this.isEditingItem = true
            },
            async editCategory(){
                if(this.editData.categoryName.trim()=='') return this.e('Category name is required')
                if(this.editData.iconImage.trim()=='') return this.e('Icon image is required')
                const res = await this.callApi('post', 'app/edit_category', this.editData)
                if (res.status===200){
                    this.CategoryLists[this.index].categoryName = this.editData.categoryName
                    //this.CategoryLists[this.index].iconImage    = this.editData.iconImage
                    this.s('Category has been updated successfully!')
                    this.editModal = false
                }else{
                    if(res.status==422){
                        if(res.data.errors.categoryName){
                            this.e(res.data.errors.categoryName[0])
                        }
                        if(res.data.errors.iconImage){
                            this.e(res.data.errors.iconImage[0])
                        }
                    }else{
                        this.swr()
                    }
                }
            },
            closeEditModal(){
                this.isEditingItem = false
                this.editModal = false
            },
            showDeletingModal(category, i){
                const deleteModalObj = {
                    showDeleteModal : true,
                    deleteUrl : 'app/delete_category',
                    data : category,
                    deletingIndex: i,
                    isDeleted : false,
                }
                this.$store.commit('setDeletingModalObj', deleteModalObj)
            },
            // async deleteCategory(){
            //     this.isDeleting = true
            //     const res = await this.callApi('post', 'app/delete_category', this.deleteItem)
            //     if(res.status===200){
            //         this.CategoryLists.splice(this.deletingIndex,1)
            //         this.s('Category has been deleted successfully!')
            //     }else{
            //         this.swr()
            //     }
            //     this.isDeleting = false
            //     this.showDeleteModal = false
            // },

            //delete image
            async deleteImage(isAdd=true){
                let image
                if(!isAdd){ //for editing....
                    this.isIconImageNew = true
                    image = this.editData.iconImage
                    this.editData.iconImage = ''
                    this.$refs.editDataUploads.clearFiles()
                }else{
                    image = this.data.iconImage
                    this.data.iconImage = ''
                    this.$refs.uploads.clearFiles()
                }
                const res = await this.callApi('post', 'app/delete_image', {imageName: image})
                if(res.status!=200){
                    this.data.iconImage = image
                    this.swr()
                }
            },

            //handle upload image
            handleSuccess (res, file) {
                res = `/uploads/${res}`
                if(this.isEditingItem){
                    return this.editData.iconImage = res
                }
                this.data.iconImage = res
            },
            handleError (res, file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: `${file.errors.file.length ? file.errors.file[0] : 'Something went wrong!'}`
                });
            },
            handleFormatError (file) {
                this.$Notice.warning({
                    title: 'The file format is incorrect',
                    desc: 'File format of ' + file.name + ' is incorrect, please select jpg or png.'
                });
            },
            handleMaxSize (file) {
                this.$Notice.warning({
                    title: 'Exceeding file size limit',
                    desc: 'File  ' + file.name + ' is too large, no more than 2M.'
                });
            },
        },
        async created(){
            this.token = window.Laravel.csrfToken
            const res = await this.callApi('get', 'app/get_category')
            if(res.status == 200){
                this.CategoryLists = res.data
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
                if(obj.isDeleted){
                    this.CategoryLists.splice(obj.deletingIndex,1)
                }
            }
        }
    }

</script>

<style scoped>

</style>
