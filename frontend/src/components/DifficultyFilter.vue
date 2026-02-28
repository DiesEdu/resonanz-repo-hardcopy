<template>
  <div class="space-y-2">
    <h3 class="mb-3 font-semibold text-gray-700">Difficulty</h3>
    <button
      v-for="difficulty in difficulties"
      :key="difficulty"
      @click="selectedDifficulty = difficulty"
      class="w-full rounded-lg px-3 py-2 text-left transition-colors"
      :class="
        selectedDifficulty === difficulty ? 'bg-indigo-100 text-indigo-700' : 'hover:bg-gray-100'
      "
    >
      {{ difficulty }}
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useMusicStore } from "@/stores/musicStore";

const musicStore = useMusicStore();
const selectedDifficulty = computed({
  get: () => musicStore.selectedDifficulty,
  set: (value) => (musicStore.selectedDifficulty = value),
});

const difficulties = ["All", "Beginner", "Intermediate", "Advanced", "Expert"];
</script>
