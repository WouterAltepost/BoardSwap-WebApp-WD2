<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const fullName = ref('')
const address = ref('')
const city = ref('')
const postalCode = ref('')
const country = ref('')
const placing = ref(false)

onMounted(() => {
  if (!authStore.isLoggedIn) {
    router.push('/login')
  }
})

const orderTotal = computed(() => {
  return cartStore.items.reduce((sum, item) => sum + item.price * item.quantity, 0)
})

function formatPrice(price) {
  return `€${Number(price).toFixed(2)}`
}

async function placeOrder() {
  placing.value = true
  try {
    await cartStore.clearCart()
    alert('Order placed successfully!')
    setTimeout(() => router.push('/'), 2000)
  } catch {
    alert('Failed to place order. Please try again.')
  } finally {
    placing.value = false
  }
}
</script>

<template>
  <div class="container py-4">
    <h1 class="mb-4">Checkout</h1>

    <div class="row">
      <!-- Shipping Form -->
      <div class="col-md-7">
        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title mb-3">Shipping Information</h5>
            <form @submit.prevent="placeOrder">
              <div class="mb-3">
                <label for="fullName" class="form-label">Full Name</label>
                <input
                  id="fullName"
                  v-model="fullName"
                  type="text"
                  class="form-control"
                  required
                />
              </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input
                  id="address"
                  v-model="address"
                  type="text"
                  class="form-control"
                  required
                />
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="city" class="form-label">City</label>
                  <input
                    id="city"
                    v-model="city"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label for="postalCode" class="form-label">Postal Code</label>
                  <input
                    id="postalCode"
                    v-model="postalCode"
                    type="text"
                    class="form-control"
                    required
                  />
                </div>
              </div>
              <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <input
                  id="country"
                  v-model="country"
                  type="text"
                  class="form-control"
                  required
                />
              </div>

              <!-- Payment Placeholder -->
              <div class="card bg-light mb-3">
                <div class="card-body text-center text-muted">
                  <p class="mb-0">Payment provider coming soon</p>
                </div>
              </div>

              <button
                type="submit"
                class="btn btn-primary btn-lg w-100"
                :disabled="placing || !cartStore.items.length"
              >
                {{ placing ? 'Placing Order...' : 'Place Order' }}
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Order Summary -->
      <div class="col-md-5">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title mb-3">Order Summary</h5>
            <ul class="list-group list-group-flush mb-3">
              <li
                v-for="item in cartStore.items"
                :key="item.id"
                class="list-group-item d-flex justify-content-between"
              >
                <span>{{ item.name }} &times; {{ item.quantity }}</span>
                <span>{{ formatPrice(item.price * item.quantity) }}</span>
              </li>
            </ul>
            <div class="d-flex justify-content-between fw-bold fs-5">
              <span>Total</span>
              <span>{{ formatPrice(orderTotal) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
