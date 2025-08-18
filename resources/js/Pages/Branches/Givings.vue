<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import { Link } from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import BranchGivingTypeSystemsManager from "../../Components/Branches/GivingTypeSystemsManager.vue";
import BranchGivingTypesEditor from "../../Components/Branches/GivingTypesEditor.vue";

const props = defineProps({
    branch: Object,
    givingTypes: Array,
    allGivingTypes: Array,
    assignedGivingTypeIds: Array,
});
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Manage Branch Givings for {{ branch.name }}</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12 mb-3">
                    <Link href="/branches" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                        <i class="fas fa-backward"></i> Back to Branches
                    </Link>
                </div>

                <div class="col-12">
                    <Card :with-card-header="true" card-title="Edit Giving Types (Church)">
                        <template #card-body>
                            <div class="card-body">
                                <BranchGivingTypesEditor :branch-id="branch.id" :all-giving-types="allGivingTypes" :assigned-giving-type-ids="assignedGivingTypeIds" />
                            </div>
                        </template>
                    </Card>
                </div>

                <div class="col-12 mt-3">
                    <Card :with-card-header="true" card-title="Manage Systems for Assigned Types">
                        <template #card-body>
                            <div v-if="(givingTypes || []).length === 0" class="alert alert-secondary mb-0">
                                No giving types were assigned to this branch.
                            </div>
                            <div v-else class="row">
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
                                                <BranchGivingTypeSystemsManager :branch-id="branch.id" :type="type" />
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
