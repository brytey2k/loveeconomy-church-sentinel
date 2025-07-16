<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";

const form = useForm({
    name: ''
});

function saveRole() {
    form.post('/roles');
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Create Role</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Roles">
                        <template #card-body>
                            <div>
                                <Link href="/roles" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Roles
                                </Link>
                            </div>

                            <form method="post" action="/roles" @submit.prevent="saveRole">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input class="form-control" v-model="form.name" required :class="{ 'is-invalid': form.errors.name }" />
                                                <span class="error invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</span>
                                            </div>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                    <!-- /.row -->
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
