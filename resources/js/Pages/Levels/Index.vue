<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import {Link, useForm} from "@inertiajs/vue3";

defineProps({
    levels: Object,
});

function deleteLevel(levelId) {
    if (confirm('Are you sure you want to delete this level?')) {
        const form = useForm();
        form.delete(`/levels/${levelId}`, {
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
            <h1 class="m-0">Levels</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Levels">
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
                                <Link href="/levels/create" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-plus"></i> Add Level
                                </Link>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table class="table table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(level, index) in levels.data" :key="level.id">
                                        <td>{{ levels.from + index }}</td>
                                        <td>{{ level.name }}</td>
                                        <td>{{ level.position }}</td>
                                        <td>
                                            <Link :href="`/levels/${level.id}/edit`" class="btn btn-info btn-sm mr-2">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </Link>
                                            <button @click.prevent="deleteLevel(`${level.id}`)" type="button"
                                                  class="btn btn-danger btn-sm">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix" v-if="levels.links.length > 3">
                                <Pagination :links="levels.links" />
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
