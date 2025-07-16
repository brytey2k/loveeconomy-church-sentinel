<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import {Link, useForm} from "@inertiajs/vue3";
import Card from "../../Components/Card.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import { ref, computed } from 'vue';

const props = defineProps({
    'role': Object,
    'all_permissions': Array,
    'role_permissions': Array,
});

// Create a map of permission IDs that are already assigned to the role
const rolePermissionIds = computed(() => {
    return props.role_permissions.map(permission => permission.id);
});

// Create a form with permissions array
const form = useForm({
    permissions: []
});

// Initialize the form with the current role permissions
const selectedPermissions = ref([]);
props.all_permissions.forEach(permission => {
    if (rolePermissionIds.value.includes(permission.id)) {
        selectedPermissions.value.push(permission.id);
    }
});

// Update form permissions when selectedPermissions changes
const updateFormPermissions = () => {
    form.permissions = selectedPermissions.value;
};

// Initialize form permissions
updateFormPermissions();

// Check/uncheck all permissions
const selectAll = ref(selectedPermissions.value.length === props.all_permissions.length);
const toggleSelectAll = () => {
    selectAll.value = !selectAll.value;
    if (selectAll.value) {
        // Select all permissions
        selectedPermissions.value = props.all_permissions.map(permission => permission.id);
    } else {
        // Deselect all permissions
        selectedPermissions.value = [];
    }
    updateFormPermissions();
};

// Watch for changes in selectedPermissions to update selectAll state
const updateSelectAllState = () => {
    selectAll.value = selectedPermissions.value.length === props.all_permissions.length;
};

// Toggle individual permission
const togglePermission = (permissionId) => {
    const index = selectedPermissions.value.indexOf(permissionId);
    if (index === -1) {
        selectedPermissions.value.push(permissionId);
    } else {
        selectedPermissions.value.splice(index, 1);
    }
    updateFormPermissions();
    updateSelectAllState();
};

// Save permissions
function savePermissions() {
    form.post(`/roles/${props.role.id}/permissions`);
}
</script>

<template>
    <AuthLayout>
        <template #page-title>
            <h1 class="m-0">Role Permissions</h1>
        </template>

        <template #page-content>
            <FluidContainerWithRow>
                <div class="col-12">
                    <Card :with-card-header="true" card-title="Role Permissions">
                        <template #card-body>
                            <div>
                                <p class="mt-3 mx-3">Add required permissions for this role</p>
                                <Link href="/roles" class="btn btn-primary float-right mt-4 mx-3 mb-1">
                                    <i class="fas fa-backward"></i> Back to Roles
                                </Link>
                            </div>

                            <form method="post" :action="`/roles/${role.id}/permissions`" @submit.prevent="savePermissions">
                                <div class="card-body table-responsive p-0">
                                    <table class="table table-hover text-nowrap">
                                        <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>
                                                <input
                                                    type="checkbox"
                                                    :checked="selectAll"
                                                    @change="toggleSelectAll"
                                                >
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(permission, index) in all_permissions" :key="permission.id">
                                            <td>{{ permission.name }}</td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    :checked="selectedPermissions.includes(permission.id)"
                                                    @change="togglePermission(permission.id)"
                                                >
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
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
