<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'

const conditions = ref([])
const location = ref('')
const updatedAt = ref('')
const loading = ref(true)
const error = ref('')

async function fetchConditions() {
  loading.value = true
  error.value = ''
  try {
    const response = await api.get('/surf-conditions')
    location.value = response.data.location
    updatedAt.value = new Date(response.data.updated_at).toLocaleString()
    conditions.value = response.data.conditions || []
  } catch (err) {
    error.value = 'Failed to load surf conditions. Please try again later.'
    conditions.value = []
  } finally {
    loading.value = false
  }
}

function directionLabel(deg) {
  if (deg === null) return '-'
  const dirs = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW']
  return dirs[Math.round(deg / 45) % 8]
}

onMounted(fetchConditions)
</script>

<template>
  <div class="container">
    <h1 class="page-title">Surf Conditions</h1>
    <p class="page-subtitle">
      Live wave data from <strong>{{ location || 'Hossegor, France' }}</strong> — one of Europe's best surf spots.
    </p>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="alert alert-danger text-center">
      {{ error }}
    </div>

    <!-- Conditions Table -->
    <div v-else>
      <p class="text-muted mb-3">Last updated: {{ updatedAt }}</p>

      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>Time</th>
              <th>Wave Height</th>
              <th>Wave Direction</th>
              <th>Wave Period</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in conditions" :key="c.time">
              <td><strong>{{ c.time }}</strong></td>
              <td>{{ c.wave_height_m !== null ? c.wave_height_m + ' m' : '-' }}</td>
              <td>{{ c.wave_direction_deg !== null ? c.wave_direction_deg + '° (' + directionLabel(c.wave_direction_deg) + ')' : '-' }}</td>
              <td>{{ c.wave_period_s !== null ? c.wave_period_s + ' s' : '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
