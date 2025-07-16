<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    level: Object,
});

const form = useForm({
    name: props.level.name,
    position: props.level.position,
});

function updateLevel() {
    form.put(`/levels/${props.level.id}`);
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Edit Level</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Edit Level">
                        <template #card-body>
                            <div>
                                <Link href="/levels" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Levels
                                </Link>
                            </div>

                            <form method="post" :action="`/levels/${props.level.id}`" @submit.prevent="updateLevel">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input class="form-control" v-model="form.name" required :class="{ 'is-invalid': form.errors.name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Position</label>
                                                <input type="number" class="form-control" v-model="form.position" required :class="{ 'is-invalid': form.errors.position }" />
                                                <span class="error invalid-feedback" v-if="form.errors.position">{{ form.errors.position }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-info float-right">Update</button>
                                </div>
                            </form>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
