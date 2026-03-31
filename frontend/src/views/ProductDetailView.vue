<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const product = ref(null)
const loading = ref(true)
const error = ref('')
const successMessage = ref('')
const adding = ref(false)

onMounted(async () => {
  try {
    const response = await api.get(`/products/${route.params.id}`)
    product.value = response.data
  } catch {
    error.value = 'Failed to load product.'
  } finally {
    loading.value = false
  }
})

async function addToCart() {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }

  adding.value = true
  try {
    await cartStore.addItem(product.value.id, 1)
    successMessage.value = 'Added to cart!'
    setTimeout(() => (successMessage.value = ''), 3000)
  } catch {
    error.value = 'Failed to add to cart.'
  } finally {
    adding.value = false
  }
}

function formatPrice(price) {
  return `€${Number(price).toFixed(2)}`
}
</script>

<template>
  <div class="container py-4">
    <RouterLink to="/products" class="btn btn-outline-secondary mb-4">
      &larr; Back to Products
    </RouterLink>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-else-if="error && !product" class="alert alert-danger">{{ error }}</div>

    <div v-else-if="product" class="row">
      <div class="col-md-6">
        <img
          :src="product.image_url || 'https://placehold.co/600x400?text=No+Image'"
          :alt="product.name"
          style="max-height: 500px; width: auto; object-fit: contain; display: block; margin: 0 auto;"
        />
      </div>
      <div class="col-md-6">
        <h1 class="mb-3">{{ product.name }}</h1>
        <p class="fs-3 fw-bold mb-3" style="color: #D97706">{{ formatPrice(product.price) }}</p>
        <p class="text-muted mb-3">{{ product.description }}</p>
        <p class="mb-4">
          <span class="fw-semibold">Stock:</span>
          <span :class="product.stock > 0 ? '' : 'text-danger'" class="ms-1" :style="product.stock > 0 ? 'color: #555' : ''">
            {{ product.stock > 0 ? `${product.stock} available` : 'Out of stock' }}
          </span>
        </p>

        <div v-if="successMessage" class="alert alert-success">{{ successMessage }}</div>
        <div v-if="error && product" class="alert alert-danger">{{ error }}</div>

        <button
          class="btn-outline"
          style="display:inline-block"
          :disabled="product.stock === 0 || adding"
          @click="addToCart"
        >
          {{ adding ? 'Adding...' : 'Add to Cart' }}
        </button>
      </div>
    </div>
  </div>
</template>
