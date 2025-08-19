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
  currencies: Array,
  reportingCurrency: String,
  branchReportingCurrency: String,
  reportingToBranchRate: Number,
})

const form = reactive({
  giving_type_id: null,
  giving_type_system_id: null,
  transaction_date: null, // yyyy-mm-dd
  month_paid_for: null, // 1..12
  year_paid_for: new Date().getFullYear(),
  amount: null,
  currency_code: null,
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

// Derived helpers
const selectedCurrency = computed(() => {
  if (!form.currency_code) return null
  return props.currencies?.find(c => c.code === form.currency_code) || null
})

const convertedAmount = computed(() => {
  const amt = Number(form.amount)
  const cur = selectedCurrency.value
  if (!cur || !isFinite(amt)) return null
  if (!cur.rate_to_reporting) return null
  return amt * Number(cur.rate_to_reporting)
})

const branchConvertedAmount = computed(() => {
  const amt = Number(form.amount)
  const cur = selectedCurrency.value
  const branchCur = props.branchReportingCurrency
  const reportingCur = props.reportingCurrency
  const rToB = props.reportingToBranchRate

  if (!cur || !isFinite(amt) || !branchCur) return null

  // If branch currency equals entered currency => identity
  if (branchCur === form.currency_code) return amt

  // Compute amount in reporting first (if possible)
  const toReporting = cur.rate_to_reporting ? (amt * Number(cur.rate_to_reporting)) : null
  if (toReporting == null) return null

  // If branch currency equals reporting => use reporting amount
  if (branchCur === reportingCur) return toReporting

  // Otherwise, multiply by reporting->branch rate if available
  if (rToB) return toReporting * Number(rToB)

  return null
})

import { router } from '@inertiajs/vue3'

function submit() {
  const payload = { ...form }
  // Normalize to strings where needed
  if (payload.amount != null) payload.amount = String(payload.amount)
  if (payload.currency_code) payload.currency_code = String(payload.currency_code)
  router.post(`/members/${props.member.id}/payments`, payload)
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

                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label class="block font-medium">Amount</label>
                      <input type="number" step="0.01" min="0" v-model.number="form.amount" class="form-control" placeholder="0.00" />
                    </div>
                    <div class="form-group col-md-6">
                      <label class="block font-medium">Currency</label>
                      <select v-model="form.currency_code" class="form-control">
                        <option value="" disabled selected>Select currency</option>
                        <option
                          v-for="cur in currencies"
                          :key="cur.code"
                          :value="cur.code"
                          :data-rate-to-reporting="cur.rate_to_reporting ?? ''"
                          :data-base-reporting-to-quote-rate="cur.base_reporting_to_quote_rate ?? ''"
                        >
                          {{ cur.code }} — {{ cur.name }}
                          <span v-if="cur.rate_to_reporting"> (1 {{ cur.code }} ≈ {{ Number(cur.rate_to_reporting).toFixed(6) }} {{ reportingCurrency }})</span>
                          <span v-else> — rate unavailable</span>
                        </option>
                      </select>
                      <small v-if="selectedCurrency && selectedCurrency.as_of_hour" class="text-muted">
                        Rate as of: {{ new Date(selectedCurrency.as_of_hour).toLocaleString() }}
                      </small>
                    </div>
                  </div>

                  <div v-if="convertedAmount !== null || branchConvertedAmount !== null" class="alert alert-info">
                    <strong>Converted:</strong>
                    <div>
                      <div v-if="convertedAmount !== null">
                        {{ Number(form.amount).toFixed(2) }} {{ form.currency_code }} ≈ {{ Number(convertedAmount).toFixed(2) }} {{ reportingCurrency }} (Reporting)
                      </div>
                      <div v-if="branchConvertedAmount !== null">
                        {{ Number(form.amount).toFixed(2) }} {{ form.currency_code }} ≈ {{ Number(branchConvertedAmount).toFixed(2) }} {{ branchReportingCurrency }} (Branch)
                      </div>
                    </div>
                  </div>

                  <div v-if="convertedAmount !== null || branchConvertedAmount !== null" class="mt-3">
                    <h5 class="mb-2">Account Transactions (preview)</h5>
                    <div class="table-responsive">
                      <table class="table table-sm table-bordered">
                        <thead>
                          <tr>
                            <th>Detail</th>
                            <th class="text-right">Amount</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Original Amount</td>
                            <td class="text-right">{{ Number(form.amount).toFixed(2) }} {{ form.currency_code }}</td>
                          </tr>
                          <tr v-if="convertedAmount !== null">
                            <td>Reporting Currency Amount ({{ reportingCurrency }})</td>
                            <td class="text-right">{{ Number(convertedAmount).toFixed(2) }} {{ reportingCurrency }}</td>
                          </tr>
                          <tr v-if="branchConvertedAmount !== null">
                            <td>Branch Currency Amount ({{ branchReportingCurrency }})</td>
                            <td class="text-right">{{ Number(branchConvertedAmount).toFixed(2) }} {{ branchReportingCurrency }}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-save"></i> Save Payment
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
