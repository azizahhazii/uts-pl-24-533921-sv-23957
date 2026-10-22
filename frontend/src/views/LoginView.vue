<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';

const email = ref('admin@gmail.com');
const password = ref('password123');
const errorMessage = ref('');
const router = useRouter();

const handleLogin = async () => {
  try {
    const res = await api.post('/login', {
      email: email.value,
      password: password.value,
    });
    
    // Menyimpan token ke LocalStorage
    localStorage.setItem('token', res.data.access_token);
    router.push('/items');
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Login gagal!';
  }
};
</script>

<template>
  <div style="max-width: 360px; margin: 80px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px; font-family: sans-serif;">
    <h2 style="text-align: center;">Login System</h2>
    <p v-if="errorMessage" style="color: red; text-align: center;">{{ errorMessage }}</p>
    <form @submit.prevent="handleLogin">
      <div style="margin-bottom: 12px;">
        <label>Email:</label>
        <input v-model="email" type="email" required style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" />
      </div>
      <div style="margin-bottom: 16px;">
        <label>Password:</label>
        <input v-model="password" type="password" required style="width: 100%; padding: 8px; margin-top: 4px; box-sizing: border-box;" />
      </div>
      <button type="submit" style="width: 100%; padding: 10px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
        Login
      </button>
    </form>
  </div>
</template>