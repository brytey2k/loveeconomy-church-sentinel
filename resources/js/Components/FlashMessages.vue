<script setup>
import {usePage} from "@inertiajs/vue3";

const page = usePage();
</script>

<template>
    <div v-if="page.props.flash.success" class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-check"></i> Success</h5>
        {{ page.props.flash.success }}
    </div>

    <div v-if="page.props.flash.error" class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-check"></i> Error</h5>
        {{ page.props.flash.error }}
    </div>
</template>
