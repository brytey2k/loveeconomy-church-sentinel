<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link, useForm} from "@inertiajs/vue3";

defineProps({
    members: Object,
});

function deleteMember(memberId) {
    if (confirm('Are you sure you want to delete this member?')) {
        const form = useForm();
        form.delete(`/members/${memberId}`, {
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
            <h1 class="m-0">Members</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Members">
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
                                <Link href="/members/create" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-plus"></i> Add Member
                                </Link>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Phone</th>
                                        <th>Branch</th>
                                        <th>Position</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(member, index) in members.data" :key="member.id">
                                        <td>{{ members.from + index }}</td>
                                        <td>{{ member.first_name }}</td>
                                        <td>{{ member.last_name }}</td>
                                        <td>{{ member.phone }}</td>
                                        <td>{{ member.branch ? member.branch.name : '-' }}</td>
                                        <td>{{ member.position ? member.position.name : '-' }}</td>
                                        <td>
                                            <Link :href="`/members/${member.id}/givings`" class="btn btn-secondary btn-sm mr-2">
                                                <i class="fas fa-donate"></i> Manage Giving Types
                                            </Link>
                                            <Link :href="`/members/${member.id}/edit`" class="btn btn-info btn-sm mr-2">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <button @click.prevent="deleteMember(`${member.id}`)" type="button"
                                                  class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="members.links.length > 3">
                                <Pagination :links="members.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
