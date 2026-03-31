<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const username = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const error = ref('')
const loading = ref(false)

async function handleSubmit() {
  error.value = ''

  if (password.value !== confirmPassword.value) {
    error.value = 'Passwords do not match.'
    return
  }

  loading.value = true
  try {
    await api.post('/auth/register', {
      username: username.value,
      email: email.value,
      password: password.value,
    })
    router.push('/login?registered=1')
  } catch (err) {
    error.value = err.response?.data?.message || 'Registration failed. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="registration-container">
    <div class="form-container">
      <div class="left-section">
        <h3 class="form-title">Register</h3>

        <div v-if="error" class="alert alert-danger">{{ error }}</div>

        <form @submit.prevent="handleSubmit" style="width: 100%">
          <input
            id="username"
            v-model="username"
            type="text"
            placeholder="Username"
            required
          />

          <input
            id="email"
            v-model="email"
            type="email"
            placeholder="Email"
            required
          />

          <input
            id="password"
            v-model="password"
            type="password"
            placeholder="Password"
            required
          />

          <input
            id="confirmPassword"
            v-model="confirmPassword"
            type="password"
            placeholder="Confirm Password"
            required
          />

          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Registering...' : 'Register' }}
          </button>
        </form>
      </div>

      <div class="right-section">
        <h3 class="welcome-title">Welcome!</h3>
        <p class="welcome-text">
          Already have an account? Log in to continue swapping boards!
        </p>
        <RouterLink to="/login" class="btn-secondary">Login</RouterLink>
      </div>
    </div>
  </div>
</template>
