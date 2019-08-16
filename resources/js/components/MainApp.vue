<template>
    <div>
        <div class="row">
            <div class="col">
                <div class="logo text-center d-flex justify-content-center align-items-center my-2">
                    <img src="/image/logo.png" alt="" width="80" height="80">
                    <h1>Gondr's Todo Application</h1>
                    <div class="login-box">
                        <button v-if="user == null" class="btn btn-outline-info" @click="loginPage">로그인</button>
                        <button v-if="user != null" class="btn btn-outline-danger" @click="logout">로그아웃</button>
                    </div>
                </div>
                <register-compo v-if="user != null"></register-compo>
            </div>
        </div>
        <div class="row d-flex justify-content-center" v-if="user != null">
            <div class="col-6">
                <ul class="menu-list">
                    <router-link to="/" tag="li" class="menu">
                        <span class="icon"><i class="fas fa-th-list"></i></span>
                        <span class="name">Todo</span>
                        <span class="line"></span>
                    </router-link>
                    <router-link to="/complete" tag="li" class="menu">
                        <span class="icon"><i class="fas fa-clipboard-check"></i></span>
                        <span class="name">Complete</span>
                        <span class="line"></span>
                    </router-link>
                    <router-link to="/calendar" tag="li" class="menu">
                        <span class="icon"><i class="fas fa-calendar-alt"></i></span>
                        <span class="name">Calendar</span>
                        <span class="line"></span>
                    </router-link>
                </ul>
            </div>
        </div>
        <div class="row" v-if="user != null">
            <div class="col">
                <transition name="page">
                    <router-view></router-view>
                </transition>
            </div>
        </div>

        <transition name="fade">
            <div id="popup" v-if="loginPopup" @click.self="loginPopup = false">
                <div class="inner">
                    <h2 class="text-center">로그인</h2>
                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="email" v-model="email" id="email" name="email" class="form-control" placeholder="이메일 입력">
                    </div>
                    <div class="form-group mb-5">
                        <label for="password">이메일</label>
                        <input type="password" v-model="password" id="password" name="password" class="form-control" placeholder="비밀번호를 입력">
                    </div>

                    <button class="btn btn-outline-success btn-block" @click="login">로그인</button>
                    <button class="btn btn-outline-danger btn-block" @click="loginPopup = false">닫기</button>
                </div>
            </div>
        </transition>

        <transition-group name="fade" tag="div" id="toastList">
            <div v-for="msg in msgList" :key="msg.id" class="gondr-toast">
                {{msg.text}}
            </div>
        </transition-group>
    </div>

</template>

<script>
    export default {
        name: "MainApp",
        data() {
            return {
                loginPopup:false,
                email:'',
                password:'',
                msgList:[],
                msgId:1
            }
        },
        computed:{
            user(){
                return this.$root.user;
            }
        },
        methods:{
            showToast(data){
                this.msgList.push(data);
                setTimeout(()=>{
                    this.msgList.splice(0, 1);
                }, 2000);
            },
            loginPage() {
                this.loginPopup = true;
            },
            logout(){
                this.$http.get('/logout')
                    .then( res => {
                        const data = res.data;
                        this.showToast({id:this.msgId++, text:data.msg});
                        if(data.success){
                            this.$root.user = null;
                        }
                    });
            },
            login(){
                this.$http.post('/login', {
                    email:this.email,
                    password:this.password
                }).then( res => {
                    const data = res.data;
                    if(!data.result){
                        this.showToast({id:this.msgId++, text:data.msg});
                    }else{
                        this.$root.user = data.user;
                        this.showToast({id:this.msgId++, text:data.msg});
                        this.loginPopup = false;
                    }
                });
            }
        }
    }
</script>

<style>
    #toastList {
        position: fixed;
        bottom:0;
        right:0;
        padding:20px;
        z-index:20;
    }
    .gondr-toast {
        color:#fff;
        background-color: #26a69a;
        padding:8px 12px;
        border-radius:0.25rem;
        min-width:250px;
        min-height:50px;
        display:flex;
        justify-content:center;
        align-items:center;
        margin-bottom:10px;
    }
    .fade-enter, .fade-leave-to {
        opacity: 0;
    }

    .fade-enter-active, .fade-leave-active {
        transition:opacity 0.5s;
    }

    .page-enter {
        opacity: 0;
        transform: translateX(100%);
    }

    .page-leave-to {
        opacity: 0;
        transform: translateX(-100%);
    }

    .page-enter-active, .page-leave-active{
        transition: all 1s;
    }

    .page-leave-active {
        position:absolute;
        width:100%;
    }

    .glist-enter, .glist-leave-to {
        opacity: 0;
        transform: translateX(-300px);
    }

    .glist-leave-active {
        position: absolute;
    }

    .menu-list {
        list-style: none;
        width:100%;
        display:flex;
        padding:0;
        margin:20px 0;
        border-radius: 20px;
        overflow: hidden;
    }

    .menu {
        background-color: #66bb6a;
        padding:0.7rem 0.9rem;
        display:flex;
        flex-direction: column;
        justify-content:center;
        align-items:center;
        line-height: 1.2;
        width:33.33%;
        color:#fff;
        transition:all 0.5s;
        position: relative;
    }

    .menu:hover {
        background-color: #388e3c;
    }

    .menu > .line {
        transition:all 0.5s;
        display:block;
        width:0px;
    }
    .menu:hover > .line {
        width:80px;
        height:2px;
        background-color: #fff;
        position:absolute;
        bottom:5px;
    }
    .menu > .icon {
        font-size:30px;
    }

    .menu > .name {
        font-size:12px;
    }

    .logo {
        height:80px;
        position: relative;
    }

    .logo .login-box {
        position: absolute;
        right:10px;
        bottom:10px;
    }

    #popup {
        position: fixed;
        width:100%;
        height:100%;
        left:0;
        top:0;
        background-color: rgba(0,0,0, 0.6);
        display:flex;
        justify-content:center;
        align-items:center;
    }

    #popup > .inner {
        width:50%;
        background-color: #fff;
        border-radius: 0.5rem;
        padding:20px;
    }
</style>
