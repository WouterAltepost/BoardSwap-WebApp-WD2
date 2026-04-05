<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api from '../../services/api'
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const products = ref([])
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)

const showModal = ref(false)
const editingProduct = ref(null)
const form = ref({ name: '', price: '', description: '', image_url: '', stock: '' })
const formLoading = ref(false)

const alert = ref({ message: '', type: '' })

function showAlert(message, type = 'success') {
  alert.value = { message, type }
  setTimeout(() => { alert.value = { message: '', type: '' } }, 4000)
}

async function fetchProducts() {
  loading.value = true
  try {
    const response = await api.get('/products', { params: { page: currentPage.value } })
    products.value = response.data.data || []
    totalPages.value = response.data.pagination?.total_pages || response.data.totalPages || 1
  } catch {
    products.value = []
  } finally {
    loading.value = false
  }
}

function openAddModal() {
  editingProduct.value = null
  form.value = { name: '', price: '', description: '', image_url: '', stock: '' }
  showModal.value = true
}

function openEditModal(product) {
  editingProduct.value = product
  form.value = {
    name: product.name,
    price: product.price,
    description: product.description || '',
    image_url: product.image_url || '',
    stock: product.stock,
  }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingProduct.value = null
}

async function handleSubmit() {
  formLoading.value = true
  try {
    const data = {
      name: form.value.name,
      price: parseFloat(form.value.price),
      description: form.value.description,
      image_url: form.value.image_url,
      stock: parseInt(form.value.stock, 10),
    }

    if (editingProduct.value) {
      await api.put(`/products/${editingProduct.value.id}`, data)
      showAlert('Product updated successfully.')
    } else {
      await api.post('/products', data)
      showAlert('Product created successfully.')
    }

    closeModal()
    await fetchProducts()
  } catch (err) {
    showAlert(err.response?.data?.message || 'Operation failed.', 'danger')
  } finally {
    formLoading.value = false
  }
}

async function deleteProduct(product) {
  if (!confirm(`Delete "${product.name}"? This cannot be undone.`)) return

  try {
    await api.delete(`/products/${product.id}`)
    showAlert('Product deleted successfully.')
    await fetchProducts()
  } catch (err) {
    showAlert(err.response?.data?.message || 'Delete failed.', 'danger')
  }
}

function goToPage(page) {
  currentPage.value = page
  fetchProducts()
}

onMounted(() => {
  if (!authStore.isAdmin) {
    router.push('/')
    return
  }
  fetchProducts()
})
</script>

<template>
  <div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <RouterLink to="/admin" class="text-decoration-none">&larr; Admin Panel</RouterLink>
        <h1 class="mb-0 mt-2">Product Management</h1>
      </div>
      <button class="btn btn-primary" @click="openAddModal">Add Product</button>
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

    <!-- Products Table -->
    <div v-else-if="products.length" class="table-responsive">
      <table class="table table-striped table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product.id">
            <td>{{ product.name }}</td>
            <td>${{ Number(product.price).toFixed(2) }}</td>
            <td>{{ product.stock }}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-outline-secondary me-2" @click="openEditModal(product)">
                Edit
              </button>
              <button class="btn btn-sm btn-outline-danger" @click="deleteProduct(product)">
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="text-center text-muted py-5">
      <p class="fs-5">No products found.</p>
    </div>

    <!-- Pagination -->
    <nav v-if="totalPages > 1" class="mt-4">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="goToPage(currentPage - 1)">Previous</button>
        </li>
        <li
          v-for="page in totalPages"
          :key="page"
          class="page-item"
          :class="{ active: page === currentPage }"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="goToPage(currentPage + 1)">Next</button>
        </li>
      </ul>
    </nav>

    <!-- Modal Backdrop + Dialog -->
    <div v-if="showModal" class="modal-backdrop fade show"></div>
    <div v-if="showModal" class="modal fade show d-block" tabindex="-1" @click.self="closeModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingProduct ? 'Edit Product' : 'Add Product' }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="handleSubmit">
            <div class="modal-body">
              <div class="mb-3">
                <label for="productName" class="form-label">Name</label>
                <input id="productName" v-model="form.name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label for="productPrice" class="form-label">Price</label>
                <input
                  id="productPrice"
                  v-model="form.price"
                  type="number"
                  class="form-control"
                  min="0"
                  step="0.01"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="productDescription" class="form-label">Description</label>
                <textarea id="productDescription" v-model="form.description" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="productImage" class="form-label">Image URL</label>
                <input id="productImage" v-model="form.image_url" type="text" class="form-control" />
              </div>
              <div class="mb-3">
                <label for="productStock" class="form-label">Stock</label>
                <input
                  id="productStock"
                  v-model="form.stock"
                  type="number"
                  class="form-control"
                  min="0"
                  required
                />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
              <button type="submit" class="btn btn-primary" :disabled="formLoading">
                {{ formLoading ? 'Saving...' : 'Save' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
