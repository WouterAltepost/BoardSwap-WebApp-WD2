<script setup>
import { RouterLink } from 'vue-router'
import { useAuthStore } from '../../stores/auth'
import { useCartStore } from '../../stores/cart'

const authStore = useAuthStore()
const cartStore = useCartStore()
</script>

<template>
  <nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container">
      <RouterLink class="navbar-brand" to="/">BoardSwap</RouterLink>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <RouterLink class="nav-link" to="/">Home</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/products">Products</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/surf-conditions">Surf Conditions</RouterLink>
          </li>
          <li class="nav-item">
            <RouterLink class="nav-link" to="/cart">
              Cart<span v-if="cartStore.itemCount > 0" class="cart-badge">({{ cartStore.itemCount }})</span>
            </RouterLink>
          </li>
          <li v-if="authStore.isAdmin" class="nav-item">
            <RouterLink class="nav-link" to="/admin">Admin Panel</RouterLink>
          </li>
        </ul>
        <ul class="navbar-nav align-items-center">
          <template v-if="authStore.isLoggedIn">
            <li class="nav-item">
              <span class="nav-link">{{ authStore.user?.name }}</span>
            </li>
            <li class="nav-item">
              <button class="btn btn-outline-light rounded-pill px-3" @click="authStore.logout()">
                Logout
              </button>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <RouterLink class="btn btn-outline-light rounded-pill px-3 me-2" to="/login">Login</RouterLink>
            </li>
            <li class="nav-item">
              <RouterLink class="btn btn-light rounded-pill px-3" style="color: #D97706" to="/register">Register</RouterLink>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>
</template>
