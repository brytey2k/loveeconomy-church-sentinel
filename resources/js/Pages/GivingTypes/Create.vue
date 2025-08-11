<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";

const form = useForm({
    key: '',
    name: '',
    description: '',
    contribution_type: 'individual',
});

function saveGivingType() {
    form.post('/giving-types');
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Create Giving Type</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Create Giving Type">
                        <template #card-body>
                            <div>
                                <Link href="/giving-types" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Giving Types
                                </Link>
                            </div>

                            <form method="post" action="/giving-types" @submit.prevent="saveGivingType">
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
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Contribution Type</label>
                                                <select class="form-control" v-model="form.contribution_type" :class="{ 'is-invalid': form.errors.contribution_type }">
                                                    <option value="individual">Individual</option>
                                                    <option value="church">Church</option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.contribution_type">{{ form.errors.contribution_type }}</span>
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
