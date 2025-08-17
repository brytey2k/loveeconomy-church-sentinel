<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link, useForm} from "@inertiajs/vue3";

const props = defineProps({
    systems: Object,
    givingTypeId: Number,
});

function deleteSystem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        const form = useForm();
        form.delete(`/giving-type-systems/${id}`);
    }
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Giving Type Systems</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Giving Type Systems">
                        <template #card-body>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Giving Type</th>
                                        <th>Parent</th>
                                        <th>Amount Low</th>
                                        <th>Amount High</th>
                                        <th>Assignable</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(s, index) in systems.data" :key="s.id">
                                        <td>{{ systems.from + index }}</td>
                                        <td>{{ s.name }}</td>
                                        <td>{{ s.giving_type?.name }}</td>
                                        <td>{{ s.parent?.name || '-' }}</td>
                                        <td>{{ s.amount_low }}</td>
                                        <td>{{ s.amount_high }}</td>
                                        <td>
                                            <span class="badge" :class="s.assignable ? 'badge-success' : 'badge-secondary'">{{ s.assignable ? 'Yes' : 'No' }}</span>
                                        </td>
                                        <td>
                                            <Link :href="`/giving-type-systems/${s.id}/edit`" class="btn btn-info btn-sm mr-2">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <button @click.prevent="deleteSystem(`${s.id}`)" type="button"
                                                    class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="systems.links.length > 3">
                                <Pagination :links="systems.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
