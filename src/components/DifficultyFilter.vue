<template>
  <div class="space-y-2">
    <h3 class="font-semibold text-gray-700 mb-3">Difficulty</h3>
    <button
      v-for="difficulty in difficulties"
      :key="difficulty"
      @click="selectedDifficulty = difficulty"
      class="w-full text-left px-3 py-2 rounded-lg transition-colors"
      :class="
        selectedDifficulty === difficulty ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-gray-100'
      "
    >
      {{ difficulty }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useMusicStore } from '@/stores/musicStore'
import { difficulties } from '@/data/mockData'

const musicStore = useMusicStore()
const selectedDifficulty = computed({
  get: () => musicStore.selectedDifficulty,
  set: (value) => (musicStore.selectedDifficulty = value),
})
</script>
