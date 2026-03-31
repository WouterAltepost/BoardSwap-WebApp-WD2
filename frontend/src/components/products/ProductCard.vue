<script setup>
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
})

function formatPrice(price) {
  return `$${Number(price).toFixed(2)}`
}

async function handleAddToCart() {
  if (!authStore.isLoggedIn) {
    localStorage.setItem('pendingCartItem', props.product.id)
    router.push('/login')
    return
  }
  await cartStore.addItem(props.product.id, 1)
}
</script>

<template>
  <div>
    <img
      :src="product.image_url || 'https://placehold.co/400x280?text=No+Image'"
      class="card-img-top"
      :alt="product.name"
    />
    <h5 class="card-title">{{ product.name }}</h5>
    <span v-if="product.stock === 0" class="badge bg-danger">Out of Stock</span>
    <p class="card-price">{{ formatPrice(product.price) }}</p>
    <div class="button-group">
      <RouterLink :to="`/products/${product.id}`" class="btn-outline">
        View Details
      </RouterLink>
      <button
        class="add-to-cart"
        :disabled="product.stock === 0"
        @click="handleAddToCart"
      >
        Add to Cart
      </button>
    </div>
  </div>
</template>
