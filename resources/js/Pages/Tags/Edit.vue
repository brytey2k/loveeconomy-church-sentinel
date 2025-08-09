<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    tag: Object,
});

const form = useForm({
    key: props.tag.key,
    name: props.tag.name,
    description: props.tag.description || '',
});

function updateTag() {
    form.put(`/tags/${props.tag.id}`);
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Edit Tag</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Edit Tag">
                        <template #card-body>
                            <div>
                                <Link href="/tags" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Tags
                                </Link>
                            </div>

                            <form method="post" :action="`/tags/${props.tag.id}`" @submit.prevent="updateTag">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Key</label>
                                                <input class="form-control" v-model="form.key" required :class="{ 'is-invalid': form.errors.key }" />
                                                <span class="error invalid-feedback" v-if="form.errors.key">{{ form.errors.key }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input class="form-control" v-model="form.name" required :class="{ 'is-invalid': form.errors.name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea class="form-control" v-model="form.description" :class="{ 'is-invalid': form.errors.description }" rows="3"></textarea>
                                                <span class="error invalid-feedback" v-if="form.errors.description">{{ form.errors.description }}</span>
                                            </div>
                                        </div>
                                    </div>
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
