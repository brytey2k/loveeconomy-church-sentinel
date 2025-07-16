<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <ul v-if="links.length > 3" class="pagination pagination-sm m-0 float-right">
        <li v-for="(link, key) in links" class="page-item" :class="{ 'active': link.active }" :key="key">
            <div v-if="link.url === null" class="page-link" v-html="link.label" />
            <Link v-else class="page-link" :href="`${link.url}`" v-html="link.label" />
        </li>
    </ul>
</template>

