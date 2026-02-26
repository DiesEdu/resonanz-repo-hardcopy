<template>
  <div class="space-y-2">
    <h3 class="font-semibold text-gray-700 mb-3">Genres</h3>
    <button
      v-for="genre in genres"
      :key="genre.id"
      @click="selectedGenre = genre.name"
      class="w-full text-left px-3 py-2 rounded-lg transition-colors flex justify-between items-center"
      :class="selectedGenre === genre.name ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-gray-100'"
    >
      <span>{{ genre.name }}</span>
      <span class="text-sm text-gray-500">{{ genre.count }}</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useMusicStore } from '@/stores/musicStore'
import { genres } from '@/data/mockData'

const musicStore = useMusicStore()
const selectedGenre = computed({
  get: () => musicStore.selectedGenre,
  set: (value) => (musicStore.selectedGenre = value),
})
</script>
