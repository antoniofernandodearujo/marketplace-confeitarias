<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Confeitaria</h1>
    <p class="mb-4">Bem-vindo à nossa confeitaria! Aqui você encontra os melhores doces e sobremesas.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <ConfetioneryForm />
    </div>

    <div class="mt-8">
      <h2 class="text-xl font-semibold mb-4">Nossos Produtos</h2>
      <!-- Aqui você pode adicionar uma lista de produtos -->
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import ConfetioneryForm from '@/Pages/Confectionery/ConfectioneryForm.vue'
</script>