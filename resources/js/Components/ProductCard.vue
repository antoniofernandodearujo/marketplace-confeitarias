<template>
  <div class="bg-black rounded-lg shadow-md p-4">
    <h3 class="text-lg font-semibold">{{ title }}</h3>
    <p class="text-gray-600">{{ description }}</p>
    <span class="text-primary font-bold">R$ {{ price }}</span>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    default: ''
  },
  price: {
    type: [Number, String],
    required: true
  }
})
</script>