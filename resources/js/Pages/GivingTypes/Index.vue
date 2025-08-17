<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link, useForm} from "@inertiajs/vue3";

const props = defineProps({
    givingTypes: Object,
    showingTrashed: Boolean,
});

function deleteGivingType(id) {
    if (confirm('Are you sure you want to delete this giving type?')) {
        const form = useForm();
        form.delete(`/giving-types/${id}`);
    }
}

function restoreGivingType(id) {
    if (confirm('Are you sure you want to restore this giving type?')) {
        const form = useForm();
        form.post(`/giving-types/${id}/restore`);
    }
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Giving Types</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Giving Types">
                        <template #card-tools>
                            <div class="card-tools">
                                <div class="btn-group mr-2">
                                    <Link :href="props.showingTrashed ? '/giving-types' : '/giving-types?trashed=1'" class="btn btn-sm" :class="props.showingTrashed ? 'btn-warning' : 'btn-default'">
                                        <i :class="props.showingTrashed ? 'fas fa-trash-restore' : 'fas fa-trash'" />
                                        {{ props.showingTrashed ? 'Showing Deleted' : 'Show Deleted' }}
                                    </Link>
                                </div>
                                <div class="input-group input-group-sm float-right" style="width: 150px;">
                                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" disabled>

                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-default" disabled>
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template #card-body>
                            <div>
                                <Link href="/giving-types/create" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-plus"></i> Add Giving Type
                                </Link>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Key</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Contribution Type</th>
                                        <th v-if="!props.showingTrashed">Actions</th>
                                        <th v-else>Restore</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(gt, index) in givingTypes.data" :key="gt.id">
                                        <td>{{ givingTypes.from + index }}</td>
                                        <td>{{ gt.key }}</td>
                                        <td>{{ gt.name }}</td>
                                        <td>{{ gt.description || '-' }}</td>
                                        <td>{{ gt.contribution_type }}</td>
                                        <td v-if="!props.showingTrashed">
                                            <Link :href="`/giving-types/${gt.id}/edit`" class="btn btn-info btn-sm mr-2">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <Link :href="`/giving-types/${gt.id}/giving-type-systems`" class="btn btn-secondary btn-sm mr-2">
                                                <i class="fas fa-eye"></i> View Systems
                                            </Link>
                                            <Link :href="`/giving-types/${gt.id}/giving-type-systems/create`" class="btn btn-primary btn-sm mr-2">
                                                <i class="fas fa-sitemap"></i> Add System
                                            </Link>
                                            <button @click.prevent="deleteGivingType(`${gt.id}`)" type="button"
                                                    class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                        <td v-else>
                                            <button @click.prevent="restoreGivingType(`${gt.id}`)" type="button" class="btn btn-success btn-sm">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="givingTypes.links.length > 3">
                                <Pagination :links="givingTypes.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
