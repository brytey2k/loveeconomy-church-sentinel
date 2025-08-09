<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link, useForm} from "@inertiajs/vue3";

const props = defineProps({
    tags: Object,
    showingTrashed: Boolean,
});

function deleteTag(tagId) {
    if (confirm('Are you sure you want to delete this tag?')) {
        const form = useForm();
        form.delete(`/tags/${tagId}`);
    }
}

function restoreTag(tagId) {
    if (confirm('Are you sure you want to restore this tag?')) {
        const form = useForm();
        form.post(`/tags/${tagId}/restore`);
    }
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Tags</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Tags">
                        <template #card-tools>
                            <div class="card-tools">
                                <div class="btn-group mr-2">
                                    <Link :href="props.showingTrashed ? '/tags' : '/tags?trashed=1'" class="btn btn-sm" :class="props.showingTrashed ? 'btn-warning' : 'btn-default'">
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
                                <Link href="/tags/create" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-plus"></i> Add Tag
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
                                        <th v-if="!props.showingTrashed">Actions</th>
                                        <th v-else>Restore</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(tag, index) in tags.data" :key="tag.id">
                                        <td>{{ tags.from + index }}</td>
                                        <td>{{ tag.key }}</td>
                                        <td>{{ tag.name }}</td>
                                        <td>{{ tag.description || '-' }}</td>
                                        <td v-if="!props.showingTrashed">
                                            <Link :href="`/tags/${tag.id}/edit`" class="btn btn-info btn-sm mr-2">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <button @click.prevent="deleteTag(`${tag.id}`)" type="button"
                                                    class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                        <td v-else>
                                            <button @click.prevent="restoreTag(`${tag.id}`)" type="button" class="btn btn-success btn-sm">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="tags.links.length > 3">
                                <Pagination :links="tags.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
