<script setup>
import AuthLayout from "../../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../../Components/FluidContainerWithRow.vue";
import Card from "../../../Components/Card.vue";
import { Link } from '@inertiajs/vue3'
import { reactive, computed } from 'vue'

const props = defineProps({
  member: Object,
  givingTypes: Array,
  systemsByGivingType: Object,
})

const form = reactive({
  giving_type_id: null,
  giving_type_system_id: null,
  transaction_date: null, // yyyy-mm-dd
  month_paid_for: null, // 1..12
  year_paid_for: new Date().getFullYear(),
  amount: null,
})

const systemsForSelectedType = computed(() => {
  if (!form.giving_type_id) return []
  return props.systemsByGivingType?.[form.giving_type_id] ?? []
})

function onGivingTypeChange() {
  // Reset the system when type changes
  form.giving_type_system_id = null
}

const months = [
  { value: 1, label: 'January' },
  { value: 2, label: 'February' },
  { value: 3, label: 'March' },
  { value: 4, label: 'April' },
  { value: 5, label: 'May' },
  { value: 6, label: 'June' },
  { value: 7, label: 'July' },
  { value: 8, label: 'August' },
  { value: 9, label: 'September' },
  { value: 10, label: 'October' },
  { value: 11, label: 'November' },
  { value: 12, label: 'December' },
]

// For now, we skip submission per requirements
function submit() {
  console.log('Preview payment payload (no submission):', { ...form })
  alert('Form submission is not implemented yet. This is a preview of your inputs in the console.')
}
</script>

<template>
  <AuthLayout>
    <template #page-title>
      <h1 class="m-0">Add Payment — {{ member.first_name }} {{ member.last_name || '' }}</h1>
    </template>

    <template #page-content>
      <FluidContainerWithRow>
        <div class="col-12 lg:col-8">
          <Card :with-card-header="true" card-title="Capture Payment">
            <template #card-tools>
              <Link :href="`/members/${member.id}/givings`" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Member Giving
              </Link>
            </template>

            <template #card-body>
              <div class="card-body">
                <form @submit.prevent="submit" class="space-y-4">
                  <div class="form-group">
                    <label class="block font-medium">Giving Type</label>
                    <select v-model.number="form.giving_type_id" @change="onGivingTypeChange" class="form-control">
                      <option value="" disabled selected>Select giving type</option>
                      <option v-for="gt in givingTypes" :key="gt.id" :value="gt.id">{{ gt.name }}</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="block font-medium">Giving Type System</label>
                    <select v-model.number="form.giving_type_system_id" class="form-control" :disabled="!form.giving_type_id">
                      <option value="" disabled selected>
                        {{ form.giving_type_id ? 'Select system' : 'Select a giving type first' }}
                      </option>
                      <option v-for="sys in systemsForSelectedType" :key="sys.id" :value="sys.id">{{ sys.name }}</option>
                    </select>
                    <small v-if="form.giving_type_id && systemsForSelectedType.length === 0" class="text-muted">
                      No systems assigned to this member for the selected giving type.
                    </small>
                  </div>

                  <div class="form-group">
                    <label class="block font-medium">Transaction Date</label>
                    <input type="date" v-model="form.transaction_date" class="form-control" />
                  </div>

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label class="block font-medium">Month Paid For</label>
                      <select v-model.number="form.month_paid_for" class="form-control">
                        <option value="" disabled selected>Select month</option>
                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label class="block font-medium">Year Paid For</label>
                      <input type="number" min="2000" :max="new Date().getFullYear() + 1" v-model.number="form.year_paid_for" class="form-control" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="block font-medium">Amount</label>
                    <input type="number" step="0.01" min="0" v-model.number="form.amount" class="form-control" placeholder="0.00" />
                  </div>

                  <div class="mt-4">
                    <button type="submit" class="btn btn-primary" :disabled="true" title="Submission disabled for now">
                      <i class="fas fa-save"></i> Save Payment (Disabled)
                    </button>
                  </div>
                </form>
              </div>
            </template>
          </Card>
        </div>
      </FluidContainerWithRow>
    </template>
  </AuthLayout>
</template>
