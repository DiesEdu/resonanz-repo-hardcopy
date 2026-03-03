<template>
  <div class="relative">
    <MagnifyingGlassIcon
      class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-amber-500"
    />
    <input
      type="text"
      placeholder="Search by title or composer..."
      v-model="searchQuery"
      class="w-full pl-10 pr-4 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
    />
  </div>
</template>

<script setup lang="ts">
import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import { useMusicStore } from '@/stores/musicStore'
import { computed } from 'vue'

const musicStore = useMusicStore()
const searchQuery = computed({
  get: () => musicStore.searchQuery,
  set: (value) => (musicStore.searchQuery = value),
})
</script>
