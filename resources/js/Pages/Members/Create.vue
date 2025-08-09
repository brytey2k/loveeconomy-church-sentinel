<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";

const props = defineProps({
    branches: Array,
    positions: Array,
    tags: Array,
});

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    branch_id: '',
    position_id: '',
    tags: [], // array of tag keys
});

function saveMember() {
    form.post('/members');
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Create Member</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Create Member">
                        <template #card-body>
                            <div>
                                <Link href="/members" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Members
                                </Link>
                            </div>

                            <form method="post" action="/members" @submit.prevent="saveMember">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input class="form-control" v-model="form.first_name" required :class="{ 'is-invalid': form.errors.first_name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input class="form-control" v-model="form.last_name" required :class="{ 'is-invalid': form.errors.last_name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Branch</label>
                                                <select class="form-control" v-model="form.branch_id" required :class="{ 'is-invalid': form.errors.branch_id }">
                                                    <option value="">Select Branch</option>
                                                    <option v-for="branch in props.branches" :key="branch.id" :value="branch.id">
                                                        {{ branch.name }}
                                                    </option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.branch_id">{{ form.errors.branch_id }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Position</label>
                                                <select class="form-control" v-model="form.position_id" required :class="{ 'is-invalid': form.errors.position_id }">
                                                    <option value="">Select Position</option>
                                                    <option v-for="position in props.positions" :key="position.id" :value="position.id">
                                                        {{ position.name }}
                                                    </option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.position_id">{{ form.errors.position_id }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Phone</label>
                                                <input class="form-control" v-model="form.phone" required :class="{ 'is-invalid': form.errors.phone }" />
                                                <span class="error invalid-feedback" v-if="form.errors.phone">{{ form.errors.phone }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Tags</label>
                                                <div class="d-flex flex-wrap">
                                                    <div class="custom-control custom-checkbox mr-4 mb-2" v-for="tag in props.tags" :key="tag.id">
                                                        <input class="custom-control-input" type="checkbox" :id="`tag_${tag.id}`" :value="tag.key" v-model="form.tags">
                                                        <label class="custom-control-label" :for="`tag_${tag.id}`">{{ tag.name }}</label>
                                                    </div>
                                                </div>
                                                <span class="error invalid-feedback d-block" v-if="form.errors.tags">{{ form.errors.tags }}</span>
                                            </div>
                                        </div>
                                    </div>
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
