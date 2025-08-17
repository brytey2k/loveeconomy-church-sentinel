<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  memberId: { type: Number, required: true },
  // type: { id, name, systems: [], attached_system_ids: [] }
  type: { type: Object, required: true },
})

// Initialize form with currently attached system ids
const form = useForm({
  system_ids: Array.isArray(props.type?.attached_system_ids)
    ? props.type.attached_system_ids.slice()
    : [],
})

function save() {
  form.post(`/members/${props.memberId}/giving-types/${props.type.id}/systems`)
}
</script>

<template>
  <div>
    <div class="d-flex flex-wrap">
      <div
        class="custom-control custom-checkbox mr-4 mb-2"
        v-for="sys in (props.type.systems || [])"
        :key="sys.id"
      >
        <input
          class="custom-control-input"
          type="checkbox"
          :id="`sys_${props.type.id}_${sys.id}`"
          :value="sys.id"
          v-model="form.system_ids"
        />
        <label class="custom-control-label" :for="`sys_${props.type.id}_${sys.id}`">
          {{ sys.name }}
          <small v-if="sys.amount_low || sys.amount_high" class="text-muted"
            >(GHS {{ sys.amount_low }} - {{ sys.amount_high }})</small
          >
          <span v-if="sys.auto_assignable" class="badge badge-info ml-1">Auto</span>
        </label>
      </div>
    </div>
    <div class="mt-2">
      <button type="button" class="btn btn-sm btn-success" @click="save">
        Save Systems
      </button>
    </div>
  </div>
</template>
