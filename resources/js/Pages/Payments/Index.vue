<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
import Pagination from "../../Components/Pagination.vue";
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  transactions: Object,
})

const rows = computed(() => {
  if (!props.transactions) return []
  // When paginated, Inertia serializes as { data: [...], links: [...], meta: {...} }
  return Array.isArray(props.transactions) ? props.transactions : (props.transactions.data ?? [])
})

const MONTHS = [
  null,
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
]

function monthName(value) {
  const n = Number(value)
  if (!Number.isInteger(n) || n < 1 || n > 12) return '-'
  return MONTHS[n]
}
</script>

<template>
  <AuthLayout>
    <template #page-title>
      <h1 class="m-0">Payments</h1>
    </template>

    <template #page-content>
      <FluidContainerWithRow>
        <div class="col-12">
          <Card :with-card-header="true" card-title="Payments">
            <template #card-tools>
              <Link href="/dashboard" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left" /> Back to Dashboard
              </Link>
            </template>
            <template #card-body>
              <div>
                <!-- optional header actions could go here -->
              </div>

              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th></th>
                      <th>Tx Date</th>
                      <th>Member</th>
                      <th>Giving Type</th>
                      <th>Giving Type System</th>
                      <th class="text-right">Amount Entered ({{ rows[0]?.currency || '' }})</th>
                      <th class="text-right">Amount ({{ rows[0]?.branch_reporting_currency || rows[0]?.reporting_currency || '' }})</th>
                      <th>Month</th>
                      <th>Year</th>
                      <th>Status</th>
                      <th>Created</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(t, index) in rows" :key="t.id">
                      <td>{{ (transactions?.from || 1) + index }}</td>
                      <td>{{ t.tx_date }}</td>
                      <td>{{ t.member ? (t.member.first_name + ' ' + (t.member.last_name || '')) : '' }}</td>
                      <td>{{ t.giving_type ? t.giving_type.name : t.giving_type_id }}</td>
                      <td>{{ t.giving_type_system ? t.giving_type_system.name : '' }}</td>
                      <td class="text-right">{{ t.entered_amount }} {{ t.currency }}</td>
                      <td class="text-right">{{ t.branch_reporting_amount }} {{ t.branch_reporting_currency || t.reporting_currency }}</td>
                      <td>{{ monthName(t.month_paid_for) }}</td>
                      <td>{{ t.year_paid_for }}</td>
                      <td>
                        <span :class="['badge', t.status_label === 'Successful' ? 'badge-success' : 'badge-warning']">{{ t.status_label }}</span>
                      </td>
                      <td>{{ t.created_at }}</td>
                    </tr>
                    <tr v-if="rows.length === 0">
                      <td colspan="11" class="text-center text-muted">No payments found.</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="card-footer clearfix" v-if="transactions && transactions.links && transactions.links.length > 3">
                <Pagination :links="transactions.links" />
              </div>
            </template>
          </Card>
        </div>
      </FluidContainerWithRow>
    </template>
  </AuthLayout>
</template>
