import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api from '../services/api'

export const useCartStore = defineStore('cart', () => {
  const items = ref([])

  const itemCount = computed(() => items.value.reduce((sum, item) => sum + item.quantity, 0))

  async function fetchCart() {
    const response = await api.get('/cart')
    items.value = response.data
  }

  async function addItem(productId, quantity) {
    await api.post('/cart', { product_id: productId, quantity })
    await fetchCart()
  }

  async function updateItem(cartItemId, quantity) {
    await api.put(`/cart/${cartItemId}`, { quantity })
    await fetchCart()
  }

  async function removeItem(cartItemId) {
    await api.delete(`/cart/${cartItemId}`)
    await fetchCart()
  }

  async function clearCart() {
    await api.delete('/cart')
    items.value = []
  }

  return { items, itemCount, fetchCart, addItem, updateItem, removeItem, clearCart }
})
