<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";

const props = defineProps({
    currency: Object,
});

const form = useForm({
    name: props.currency.name,
    short_name: props.currency.short_name,
    symbol: props.currency.symbol,
});

function updateCurrency() {
    form.put(`/currencies/${props.currency.id}`);
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Edit Currency</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Edit Currency">
                        <template #card-body>
                            <div>
                                <Link href="/currencies" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Currencies
                                </Link>
                            </div>

                            <form method="post" :action="`/currencies/${currency.id}`" @submit.prevent="updateCurrency">
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
                                                <label>Short Name</label>
                                                <input class="form-control" v-model="form.short_name" required :class="{ 'is-invalid': form.errors.short_name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.short_name">{{ form.errors.short_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Symbol</label>
                                                <input class="form-control" v-model="form.symbol" required :class="{ 'is-invalid': form.errors.symbol }" />
                                                <span class="error invalid-feedback" v-if="form.errors.symbol">{{ form.errors.symbol }}</span>
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
