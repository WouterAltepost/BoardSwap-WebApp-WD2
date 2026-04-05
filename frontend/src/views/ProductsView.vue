<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../services/api'
import ProductCard from '../components/products/ProductCard.vue'

const products = ref([])
const loading = ref(true)
const search = ref('')
const page = ref(1)
const pagination = ref({ total: 0, page: 1, limit: 12, total_pages: 1 })

let searchTimeout = null

async function fetchProducts() {
  loading.value = true
  try {
    const params = { page: page.value, limit: 12 }
    if (search.value.trim()) {
      params.search = search.value.trim()
    }
    const response = await api.get('/products', { params })
    products.value = response.data.data || []
    pagination.value = response.data.pagination || { total: 0, page: 1, limit: 12, total_pages: 1 }
  } catch {
    products.value = []
  } finally {
    loading.value = false
  }
}

function onSearchInput() {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    page.value = 1
    fetchProducts()
  }, 400)
}

function prevPage() {
  if (page.value > 1) {
    page.value--
    fetchProducts()
  }
}

function nextPage() {
  if (page.value < pagination.value.total_pages) {
    page.value++
    fetchProducts()
  }
}

onMounted(fetchProducts)
</script>

<template>
  <div class="container">
    <h1 class="page-title">Available Surfboards</h1>
    <p class="page-subtitle">
      A team based in Amsterdam with partners across the world, working with the best shapers and brands to offer and ship high-quality surfboards anywhere.
    </p>

    <!-- Search -->
    <div class="row mb-4">
      <div class="col-md-6 mx-auto">
        <input
          v-model="search"
          @input="onSearchInput"
          type="text"
          class="form-control"
          placeholder="Search surfboards by name or description..."
        />
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-else-if="products.length" class="row">
      <div v-for="product in products" :key="product.id" class="col-12 col-sm-6 col-lg-4 mb-4">
        <ProductCard :product="product" />
      </div>
    </div>

    <!-- No Results -->
    <div v-else class="text-center text-muted py-5">
      <p class="fs-5">No products found.</p>
    </div>

    <!-- Pagination -->
    <nav v-if="pagination.total_pages > 1" class="d-flex justify-content-center mt-4">
      <ul class="pagination">
        <li class="page-item" :class="{ disabled: page <= 1 }">
          <button class="page-link" @click="prevPage">&laquo; Previous</button>
        </li>
        <li class="page-item disabled">
          <span class="page-link">Page {{ pagination.page }} of {{ pagination.total_pages }}</span>
        </li>
        <li class="page-item" :class="{ disabled: page >= pagination.total_pages }">
          <button class="page-link" @click="nextPage">Next &raquo;</button>
        </li>
      </ul>
    </nav>
  </div>
</template>
