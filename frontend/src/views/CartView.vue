<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const loading = ref(true)

onMounted(async () => {
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }
  try {
    await cartStore.fetchCart()
  } finally {
    loading.value = false
  }
})

const cartTotal = computed(() => {
  return cartStore.items.reduce((sum, item) => sum + item.price * item.quantity, 0)
})

function formatPrice(price) {
  return `€${Number(price).toFixed(2)}`
}

async function updateQuantity(item, newQty) {
  const qty = parseInt(newQty)
  if (qty > 0) {
    await cartStore.updateItem(item.id, qty)
  }
}

async function removeItem(item) {
  await cartStore.removeItem(item.id)
}
</script>

<template>
  <div class="container py-4">
    <h1 class="mb-4">Shopping Cart</h1>

    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <template v-else>
      <!-- Empty Cart -->
      <div v-if="!cartStore.items.length" class="text-center py-5">
        <h4 class="text-muted mb-3">Your cart is empty</h4>
        <p class="text-muted mb-4">Looks like you haven't added any items yet.</p>
        <RouterLink to="/products" class="btn btn-primary">Continue Shopping</RouterLink>
      </div>

      <!-- Cart Items -->
      <template v-else>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th style="width: 120px">Quantity</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in cartStore.items" :key="item.id">
                <td>
                  <div class="d-flex align-items-center">
                    <img
                      :src="item.image_url || 'https://placehold.co/60x60?text=No+Image'"
                      :alt="item.name"
                      class="rounded me-3"
                      style="width: 60px; height: 60px; object-fit: cover"
                    />
                    <span>{{ item.name }}</span>
                  </div>
                </td>
                <td>{{ formatPrice(item.price) }}</td>
                <td>
                  <input
                    type="number"
                    class="form-control form-control-sm"
                    :value="item.quantity"
                    min="1"
                    @change="updateQuantity(item, $event.target.value)"
                  />
                </td>
                <td class="fw-semibold">{{ formatPrice(item.price * item.quantity) }}</td>
                <td>
                  <button class="btn btn-outline-danger btn-sm" @click="removeItem(item)">
                    Remove
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Cart Footer -->
        <div class="d-flex justify-content-between align-items-center mt-4">
          <RouterLink to="/products" class="btn btn-outline-secondary">
            Continue Shopping
          </RouterLink>
          <div class="text-end">
            <p class="fs-4 fw-bold mb-2">Total: {{ formatPrice(cartTotal) }}</p>
            <RouterLink
              to="/checkout"
              class="btn btn-primary btn-lg"
              :class="{ disabled: !cartStore.items.length }"
            >
              Proceed to Checkout
            </RouterLink>
          </div>
        </div>
      </template>
    </template>
  </div>
</template>
