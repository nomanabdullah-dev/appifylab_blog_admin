<template>
    <div>
        <div class="container-fluid">
            <div class="_1adminOverveiw_table_recent _box_shadow _border_radious _mar_b30 _p20 col-md-4">
                <div class="login_header"><h2>Login To The Dashboard</h2></div>
                <Input type="email" v-model="data.email" placeholder="Email" style="margin-bottom:8px"/>
                <Input type="password" v-model="data.password" placeholder="*********" style="margin-bottom:7px"/>
                <div class="login_footer">
                    <Button type="primary" @click="login" :disabled="isLogging" :loading="isLogging">{{isLogging ? 'Loging...' : 'Login'}}</Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                data : {
                    email : '',
                    password : ''
                },
                isLogging : false
            }
        },
        methods : {
            async login(){
                if(this.data.email.trim()=='') return this.e('Email is required')
                if(this.data.password.trim()=='') return this.e('Password is required')
                if(this.data.password.length < 6) return this.e('Password should be more then 5 characters')
                this.isLogging = true
                const res = await this.callApi('post', 'app/admin_login', this.data)
                if(res.status===200){
                    this.s(res.data.msg)
                    window.location = '/'
                }else{
                    if(res.status===401){
                        this.i(res.data.msg)
                    }else if(res.status==422){
                        for(let i in res.data.errors){
                            this.e(res.data.errors[i][0])
                        }
                    }else{
                        this.swr()
                    }
                }
                this.isLogging = false
            }
        }
    }
</script>

<style scoped>
    ._1adminOverveiw_table_recent{
        margin: 0 auto;
        margin-top: 180px;
    }
    .login_footer{
        text-align: center;
        margin-top: 10px;
    }
    .login_header{
        text-align: center;
        margin-bottom: 20px;
    }
</style>
