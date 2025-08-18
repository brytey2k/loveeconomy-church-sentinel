<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  memberId: { type: Number, required: true },
  // allGivingTypes: [{id, name, key, auto_assignable}],
  allGivingTypes: { type: Array, required: true },
  // assignedGivingTypeIds: [id]
  assignedGivingTypeIds: { type: Array, required: true },
})

const form = useForm({
  giving_type_ids: Array.isArray(props.assignedGivingTypeIds)
    ? props.assignedGivingTypeIds.slice()
    : [],
})

function save() {
  form.post(`/members/${props.memberId}/giving-types`)
}
</script>

<template>
  <div>
    <div class="d-flex flex-wrap">
      <div
        class="custom-control custom-checkbox mr-4 mb-2"
        v-for="gt in (props.allGivingTypes || [])"
        :key="gt.id"
      >
        <input
          class="custom-control-input"
          type="checkbox"
          :id="`gt_${gt.id}`"
          :value="gt.id"
          v-model="form.giving_type_ids"
        />
        <label class="custom-control-label" :for="`gt_${gt.id}`">
          {{ gt.name }}
          <span v-if="gt.auto_assignable" class="badge badge-info ml-1">Auto</span>
        </label>
      </div>
    </div>
    <div class="mt-2">
      <button type="button" class="btn btn-sm btn-success" @click="save">
        Save Giving Types
      </button>
    </div>
  </div>
</template>
