<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link} from "@inertiajs/vue3";

defineProps({
    roles: Object,
})

function confirmDelete() {
    return confirm('Are you sure you want to delete this role?');
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Roles</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Roles">
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
                                <Link href="/roles/create" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-plus"></i> Add Role
                                </Link>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(role, index) in roles.data" :key="role.id">
                                        <td>{{ roles.from + index }}</td>
                                        <td>{{ role.name }}</td>
                                        <td>
                                            <Link :href="`/roles/${role.id}/permissions`" class="btn btn-info btn-sm">
                                                <i class="fas fa-pencil-alt"></i> Permissions
                                            </Link>
                                            <Link :href="`/roles/${role.id}/edit`" class="btn btn-info btn-sm ml-2">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <Link @click.prevent="confirmDelete" :href="`/roles/${role.id}`" method="delete" class="btn btn-danger btn-sm ml-2">
                                                <i class="fas fa-trash"></i> Delete
                                            </Link>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="roles.links.length > 3">
                                <Pagination :links="roles.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
