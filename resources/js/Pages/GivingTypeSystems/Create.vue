<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import { watch } from "vue";

const props = defineProps({
    parentOptions: Array,
    givingTypeId: Number,
});

const form = useForm({
    giving_type_id: props.givingTypeId || '',
    parent_id: '',
    name: '',
    amount_low: '',
    amount_high: '',
    assignable: true,
    auto_assignable: false,
});

watch(() => form.assignable, (newVal) => {
    if (!newVal) {
        form.auto_assignable = false;
        form.amount_low = '';
        form.amount_high = '';
    }
});

function saveItem() {
    form.post('/giving-type-systems');
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Create Giving Type System</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Create Giving Type System">
                        <template #card-body>
                            <div>
                                <Link :href="props.givingTypeId ? `/giving-types/${props.givingTypeId}/giving-type-systems` : '/giving-type-systems'" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to List
                                </Link>
                            </div>

                            <form method="post" action="/giving-type-systems" @submit.prevent="saveItem">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Parent (Optional)</label>
                                                <select class="form-control" v-model="form.parent_id" :class="{ 'is-invalid': form.errors.parent_id }">
                                                    <option value="">-- none --</option>
                                                    <option v-for="opt in props.parentOptions" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                                                </select>
                                                <span class="error invalid-feedback" v-if="form.errors.parent_id">{{ form.errors.parent_id }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input class="form-control" v-model="form.name" required :class="{ 'is-invalid': form.errors.name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Amount Low</label>
                                                <input type="number" step="0.01" min="0.01" class="form-control" v-model="form.amount_low" :disabled="!form.assignable" :class="{ 'is-invalid': form.errors.amount_low }" />
                                                <span class="error invalid-feedback" v-if="form.errors.amount_low">{{ form.errors.amount_low }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Amount High</label>
                                                <input type="number" step="0.01" min="0.01" class="form-control" v-model="form.amount_high" :disabled="!form.assignable" :class="{ 'is-invalid': form.errors.amount_high }" />
                                                <span class="error invalid-feedback" v-if="form.errors.amount_high">{{ form.errors.amount_high }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Assignable</label>
                                                <select class="form-control" v-model="form.assignable">
                                                    <option :value="true">Yes</option>
                                                    <option :value="false">No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Auto Assignable</label>
                                                <select class="form-control" v-model="form.auto_assignable" :disabled="!form.assignable">
                                                    <option :value="false">No</option>
                                                    <option :value="true">Yes</option>
                                                </select>
                                                <small v-if="!form.assignable" class="form-text text-muted">Enable Assignable to change this</small>
                                                <span class="error invalid-feedback" v-if="form.errors.auto_assignable">{{ form.errors.auto_assignable }}</span>
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
