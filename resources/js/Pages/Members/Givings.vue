<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import { Link } from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import GivingTypeSystemsManager from "../../Components/Members/GivingTypeSystemsManager.vue";

const props = defineProps({
    member: Object,
    givingTypes: Array,
});
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Manage Givings for {{ member.first_name }} {{ member.last_name }}</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12 mb-3">
                    <Link href="/members" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                        <i class="fas fa-backward"></i> Back to Members
                    </Link>
                </div>

                <div class="col-12" v-if="(givingTypes || []).length === 0">
                    <Card :with-card-header="true" card-title="Assigned Giving Types">
                        <template #card-body>
                            <p>No giving types were assigned to this member.</p>
                        </template>
                    </Card>
                </div>

                <div class="col-12" v-else>
                    <Card :with-card-header="true" card-title="Assigned Giving Types (Individual)">
                        <template #card-body>
                            <div class="row">
                                <div class="col-md-6 mb-3" v-for="type in givingTypes" :key="type.id">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <strong>{{ type.name }}</strong>
                                        </div>
                                        <div class="card-body">
                                            <template v-if="(type.systems || []).length === 0">
                                                <div class="alert alert-secondary mb-0">
                                                    No assignable systems available under this giving type.
                                                </div>
                                            </template>
                                            <template v-else>
                                                <GivingTypeSystemsManager :member-id="member.id" :type="type" />
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </FluidContainerWithRow>
        </template>
    </AuthLayout>
</template>
