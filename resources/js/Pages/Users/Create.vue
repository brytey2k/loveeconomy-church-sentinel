<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function saveUser() {
    form.post('/users');
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Create User</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Users">
                        <template #card-body>
                            <div>
                                <Link href="/users" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Users
                                </Link>
                            </div>

                            <form method="post" action="/users" @submit.prevent="saveUser">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input required class="form-control" v-model="form.name" :class="{ 'is-invalid': form.errors.name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input required type="email" class="form-control" v-model="form.email" :class="{ 'is-invalid': form.errors.email }" />
                                                <span class="error invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input required type="password" class="form-control" v-model="form.password" :class="{ 'is-invalid': form.errors.password }" />
                                                <span class="error invalid-feedback" v-if="form.errors.password">{{ form.errors.password }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input required type="password" class="form-control" v-model="form.password_confirmation" :class="{ 'is-invalid': form.errors.password_confirmation }" />
                                                <span class="error invalid-feedback" v-if="form.errors.password_confirmation">{{ form.errors.password_confirmation }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info float-right">Save</button>
                                </div>
                            </form>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
