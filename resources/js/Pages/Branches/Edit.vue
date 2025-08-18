<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    branch: Object,
    levels: Array,
    countries: Array,
    branches: Array,
    tags: Array,
    currencies: Array,
});

const form = useForm({
    name: props.branch.name,
    level_id: props.branch.level_id,
    country_id: props.branch.country_id,
    currency: props.branch.currency || 'GHS',
    parent_id: props.branch.parent_id || '',
});

function updateBranch() {
    form.put(`/branches/${props.branch.id}`);
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Edit Branch</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Edit Branch">
                        <template #card-body>
                            <div>
                                <Link href="/branches" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Branches
                                </Link>
                            </div>

                            <form method="post" :action="`/branches/${props.branch.id}`" @submit.prevent="updateBranch">
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
                                                <label>Level</label>
                                                <select class="form-control" v-model="form.level_id" required :class="{ 'is-invalid': form.errors.level_id }">
                                                    <option value="">Select Level</option>
                                                    <option v-for="level in props.levels" :key="level.id" :value="level.id">
                                                        {{ level.name }}
                                                    </option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.level_id">{{ form.errors.level_id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Country</label>
                                                <select class="form-control" v-model="form.country_id" required :class="{ 'is-invalid': form.errors.country_id }">
                                                    <option value="">Select Country</option>
                                                    <option v-for="country in props.countries" :key="country.id" :value="country.id">
                                                        {{ country.name }}
                                                    </option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.country_id">{{ form.errors.country_id }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Currency</label>
                                                <select class="form-control" v-model="form.currency" required :class="{ 'is-invalid': form.errors.currency }">
                                                    <option value="">Select Currency</option>
                                                    <option v-for="c in props.currencies" :key="c.id" :value="c.short_name">
                                                        {{ c.name }} ({{ c.short_name }})
                                                    </option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.currency">{{ form.errors.currency }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Parent Branch</label>
                                                <select class="form-control" v-model="form.parent_id" :class="{ 'is-invalid': form.errors.parent_id }">
                                                    <option value="">None</option>
                                                    <option v-for="branch in props.branches" :key="branch.id" :value="branch.id">
                                                        {{ branch.name }}
                                                    </option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.parent_id">{{ form.errors.parent_id }}</span>
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
