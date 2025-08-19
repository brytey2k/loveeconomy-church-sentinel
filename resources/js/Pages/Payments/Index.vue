<script setup>
import AuthLayout from "../../Layouts/AuthLayout.vue";
import FluidContainerWithRow from "../../Components/FluidContainerWithRow.vue";
import Card from "../../Components/Card.vue";
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
</script>

<template>
  <AuthLayout>
    <template #page-title>
      <h1 class="m-0">Payments</h1>
    </template>

    <template #page-content>
      <FluidContainerWithRow>
        <div class="col-12">
          <Card :with-card-header="true" card-title="All Payments">
            <template #card-tools>
              <Link href="/dashboard" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left" /> Back to Dashboard
              </Link>
            </template>
            <template #card-body>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-sm table-bordered">
                    <thead>
                      <tr>
                        <th>ID</th>
                        <th>Tx Date</th>
                        <th>Member</th>
                        <th>Giving Type</th>
                        <th class="text-right">Amount ({{ rows[0]?.reporting_currency || '' }})</th>
                        <th>Status</th>
                        <th>Created</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="t in rows" :key="t.id">
                        <td>{{ t.id }}</td>
                        <td>{{ t.tx_date }}</td>
                        <td>{{ t.member ? (t.member.first_name + ' ' + (t.member.last_name || '')) : '' }}</td>
                        <td>{{ t.giving_type ? t.giving_type.name : t.giving_type_id }}</td>
                        <td class="text-right">{{ t.reporting_amount }} {{ t.reporting_currency }}</td>
                        <td>{{ t.status }}</td>
                        <td>{{ t.created_at }}</td>
                      </tr>
                      <tr v-if="rows.length === 0">
                        <td colspan="7" class="text-center text-muted">No payments found.</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div v-if="transactions && transactions.links" class="mt-3 d-flex flex-wrap gap-2">
                  <Link v-for="l in transactions.links" :key="(l.url || '') + (l.label || '')" :href="l.url || '#'" :class="['btn btn-sm', {'btn-primary': l.active, 'btn-outline-primary': !l.active, 'disabled': !l.url}]" v-html="l.label" />
                </div>
              </div>
            </template>
          </Card>
        </div>
      </FluidContainerWithRow>
    </template>
  </AuthLayout>
</template>
