<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const totalProducts = ref(0)
const totalUsers = ref(0)
const loading = ref(true)

onMounted(async () => {
  if (!authStore.isAdmin) {
    router.push('/')
    return
  }

  try {
    const [productsRes, usersRes] = await Promise.all([
      api.get('/products', { params: { limit: 1 } }),
      api.get('/users'),
    ])
    totalProducts.value = productsRes.data.pagination?.total || productsRes.data.total || 0
    totalUsers.value = Array.isArray(usersRes.data) ? usersRes.data.length : usersRes.data.total || 0
  } catch {
    // silently fail, counts stay at 0
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="container py-4">
    <h1 class="section-title">Admin Panel</h1>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <template v-else>
      <!-- Stat Cards -->
      <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
          <div class="stat-card">
            <div class="stat-label">Welcome</div>
            <div class="stat-number">{{ authStore.user?.username || 'Admin' }}</div>
            <div class="stat-desc">Logged in as administrator</div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="stat-card">
            <div class="stat-label">Total Products</div>
            <div class="stat-number">{{ totalProducts }}</div>
            <div class="stat-desc">Listed on BoardSwap</div>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="stat-card">
            <div class="stat-label">Total Users</div>
            <div class="stat-number">{{ totalUsers }}</div>
            <div class="stat-desc">Registered accounts</div>
          </div>
        </div>
      </div>

      <!-- Manage Section -->
      <h2 class="section-title">Manage</h2>

      <div class="row g-4">
        <div class="col-12 col-md-6">
          <div class="action-card">
            <div class="action-icon">🏄</div>
            <h5 style="font-weight: bold; margin-bottom: 8px;">Product Management</h5>
            <p style="color: #888; margin-bottom: 16px;">Add, edit, and delete products from the catalog.</p>
            <RouterLink to="/admin/products" class="btn-outline">
              Manage Products
            </RouterLink>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="action-card">
            <div class="action-icon">👥</div>
            <h5 style="font-weight: bold; margin-bottom: 8px;">User Management</h5>
            <p style="color: #888; margin-bottom: 16px;">View users, change roles, and manage accounts.</p>
            <RouterLink to="/admin/users" class="btn-outline">
              Manage Users
            </RouterLink>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<style scoped>
.stat-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  padding: 25px;
  text-align: center;
}
.stat-label {
  font-size: 0.8rem;
  font-weight: bold;
  text-transform: uppercase;
  letter-spacing: 1px;
  color: #D97706;
  margin-bottom: 8px;
}
.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: #333;
  margin-bottom: 4px;
}
.stat-desc {
  font-size: 0.9rem;
  color: #888;
}
.section-title {
  font-size: 1.4rem;
  font-weight: bold;
  color: #333;
  border-bottom: 3px solid #D97706;
  display: inline-block;
  padding-bottom: 4px;
  margin-bottom: 20px;
  margin-top: 35px;
}
.action-card {
  background: white;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.08);
  padding: 30px 25px;
  text-align: center;
}
.action-icon {
  font-size: 2.5rem;
  margin-bottom: 12px;
}
</style>
