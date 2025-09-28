<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link, useForm, usePage} from "@inertiajs/vue3";
import { computed } from 'vue'

defineProps({
    users: Object,
})

const page = usePage()
const permissions = computed(() => page.props?.auth?.permissions ?? [])
function can(permission) {
    if (!permission) return true
    return Array.isArray(permissions.value) && permissions.value.includes(permission)
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        const form = useForm();
        form.delete(`/users/${userId}`, {
            onError: (errors) => {
                // Handle errors if needed
                console.error(errors);
            }
        });
    }
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Users</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Users">
                        <template #card-tools>
                            <div class="card-tools">
                                <div class="input-group input-group-sm float-right" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">

                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #card-body>
                            <div>
                                <Link href="/users/create" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-plus"></i> Add User
                                </Link>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(user, index) in users.data" :key="user.id">
                                        <td>{{ users.from + index }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td>
                                            <Link :href="`/users/${user.id}/edit`" class="btn btn-info btn-sm">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <Link v-if="can('manage users')" :href="`/users/${user.id}/roles`" class="btn btn-warning btn-sm ml-2">
                                                <i class="fas fa-user-shield"></i> Add Roles
                                            </Link>
                                            <button @click.prevent="deleteUser(user.id)" class="btn btn-danger btn-sm ml-2">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="users.links.length > 3">
                                <Pagination :links="users.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
