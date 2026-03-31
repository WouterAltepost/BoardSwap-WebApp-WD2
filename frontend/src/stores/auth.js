import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import router from '../router'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(JSON.parse(localStorage.getItem('user')))
  const token = ref(localStorage.getItem('token'))

  const isLoggedIn = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')

  function login(userData, tokenValue) {
    user.value = userData
    token.value = tokenValue
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('token', tokenValue)
  }

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
    router.push('/login')
  }

  return { user, token, isLoggedIn, isAdmin, login, logout }
})
