<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const users = ref([])
const loading = ref(true)

const alert = ref({ message: '', type: '' })

function showAlert(message, type = 'success') {
  alert.value = { message, type }
  setTimeout(() => { alert.value = { message: '', type: '' } }, 4000)
}

async function fetchUsers() {
  loading.value = true
  try {
    const response = await api.get('/users')
    users.value = Array.isArray(response.data) ? response.data : response.data.users || []
  } catch {
    users.value = []
  } finally {
    loading.value = false
  }
}

async function toggleRole(user) {
  const newRole = user.role === 'admin' ? 'user' : 'admin'
  try {
    await api.put(`/users/${user.id}/role`, { role: newRole })
    showAlert(`${user.username} is now ${newRole === 'admin' ? 'an admin' : 'a user'}.`)
    await fetchUsers()
  } catch (err) {
    showAlert(err.response?.data?.message || 'Failed to update role.', 'danger')
  }
}

async function deleteUser(user) {
  if (!confirm(`Delete user "${user.username}"? This cannot be undone.`)) return

  try {
    await api.delete(`/users/${user.id}`)
    showAlert(`User "${user.username}" deleted.`)
    await fetchUsers()
  } catch (err) {
    showAlert(err.response?.data?.message || 'Delete failed.', 'danger')
  }
}

function isCurrentUser(user) {
  return authStore.user?.id === user.id
}

function formatDate(dateString) {
  if (!dateString) return '-'
  return new Date(dateString).toLocaleDateString()
}

onMounted(() => {
  if (!authStore.isAdmin) {
    router.push('/')
    return
  }
  fetchUsers()
})
</script>

<template>
  <div class="container py-4">
    <div class="mb-4">
      <RouterLink to="/admin" class="text-decoration-none">&larr; Admin Panel</RouterLink>
      <h1 class="mb-0 mt-2">User Management</h1>
    </div>

    <!-- Alert -->
    <div v-if="alert.message" class="alert" :class="`alert-${alert.type}`">
      {{ alert.message }}
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Users Table -->
    <div v-else-if="users.length" class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>
              {{ user.username }}
              <span v-if="isCurrentUser(user)" class="badge bg-secondary ms-1">You</span>
            </td>
            <td>{{ user.email }}</td>
            <td>
              <span class="badge" :class="user.role === 'admin' ? 'bg-danger' : 'bg-primary'">
                {{ user.role }}
              </span>
            </td>
            <td>{{ formatDate(user.created_at) }}</td>
            <td class="text-end">
              <button
                class="btn btn-sm me-2"
                :class="user.role === 'admin' ? 'btn-outline-warning' : 'btn-outline-success'"
                @click="toggleRole(user)"
              >
                {{ user.role === 'admin' ? 'Make User' : 'Make Admin' }}
              </button>
              <button
                v-if="!isCurrentUser(user)"
                class="btn btn-sm btn-outline-danger"
                @click="deleteUser(user)"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center text-muted py-5">
      <p class="fs-5">No users found.</p>
    </div>
  </div>
</template>
