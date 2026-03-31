<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()
const route = useRoute()
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const showRegisteredMessage = computed(() => route.query.registered === '1')

async function handleSubmit() {
  error.value = ''
  loading.value = true
  try {
    const response = await api.post('/auth/login', {
      email: email.value,
      password: password.value,
    })
    authStore.login(response.data.user, response.data.token)

    const pendingCartItem = localStorage.getItem('pendingCartItem')
    if (pendingCartItem) {
      localStorage.removeItem('pendingCartItem')
      await cartStore.addItem(Number(pendingCartItem), 1)
      router.push('/products')
    } else {
      router.push('/')
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Login failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-container">
    <div class="login-box">
      <div class="login-left">
        <h3 class="login-title">Login</h3>

        <div v-if="showRegisteredMessage" class="alert alert-success">
          Registration successful! Please log in.
        </div>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <form @submit.prevent="handleSubmit" style="width: 100%">
          <input
            id="email"
            v-model="email"
            type="email"
            class="login-input"
            placeholder="Email"
            required
          />

          <input
            id="password"
            v-model="password"
            type="password"
            class="login-input"
            placeholder="Password"
            required
          />

          <button type="submit" class="login-button" :disabled="loading">
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>
        </form>
      </div>

      <div class="login-right">
        <h3 class="welcome-title">Welcome Back!</h3>
        <p class="welcome-text">
          Don't have an account yet? Register now and start swapping boards!
        </p>
        <RouterLink to="/register" class="register-button">Register</RouterLink>
      </div>
    </div>
  </div>
</template>
