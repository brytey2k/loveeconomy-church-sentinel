<script setup>
defineProps({
    withCardHeader: {
        type: Boolean,
        default: false
    },
    cardTitle: {
        type: String,
        default: 'Card Title'
    }
})
</script>

<template>
    <div class="card">
        <div v-if="withCardHeader === true" class="card-header">
            <h3 v-if="cardTitle" class="card-title">{{ cardTitle }}</h3>

            <slot name="card-tools" />
        </div>

        <slot name="card-body" />
    </div>

</template>
