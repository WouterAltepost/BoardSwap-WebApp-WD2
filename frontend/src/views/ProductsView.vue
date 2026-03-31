<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import ProductCard from '../components/products/ProductCard.vue'

const products = ref([])
const loading = ref(true)

async function fetchProducts() {
  loading.value = true
  try {
    const response = await api.get('/products')
    products.value = response.data.data || []
  } catch {
    products.value = []
  } finally {
    loading.value = false
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

    <!-- Loading -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-else-if="products.length" class="row">
      <div v-for="product in products" :key="product.id" class="col-lg-3 col-md-4 col-sm-6 mb-4">
        <ProductCard :product="product" />
      </div>
    </div>

    <!-- No Results -->
    <div v-else class="text-center text-muted py-5">
      <p class="fs-5">No products found.</p>
    </div>
  </div>
</template>
