<script setup>
import {useForm} from "@inertiajs/vue3";
import {onMounted} from "vue";

onMounted(() => {
    document.body.classList.remove(['sidebar-mini']);
    document.body.classList.add(['login-page']);
});

const form = useForm({
    email: null,
    password: null,
    remember: false
})

function handleLogin() {
    form.post('/login');
}
</script>

<template>
    <div class="login-box">
        <div class="login-logo">
            <a href="/"><b>Admin</b>LTE</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="/login" method="post" @submit.prevent="handleLogin">
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Email" v-model="form.email" :class="{ 'is-invalid': form.errors.email }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        <span class="error invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</span>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control" placeholder="Password" v-model="form.password" :class="{ 'is-invalid': form.errors.password }" />
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        <span class="error invalid-feedback" v-if="form.errors.password">{{ form.errors.password }}</span>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" id="remember" v-model="form.remember" />
                                <label for="remember">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <p class="mt-4 text-center">
                    <a href="#">I forgot my password</a>
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
</template>
