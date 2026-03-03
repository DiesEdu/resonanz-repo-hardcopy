<template>
  <div class="space-y-4">
    <!-- Skeleton card -->
    <div
      v-for="i in count"
      :key="i"
      class="animate-pulse overflow-hidden rounded-lg bg-white shadow-md"
    >
      <!-- Image placeholder -->
      <div class="shimmer h-48 w-full bg-gradient-to-r from-yellow-200 to-yellow-300"></div>

      <div class="space-y-3 p-4">
        <!-- Title placeholder -->
        <div class="shimmer h-5 w-3/4 rounded bg-yellow-200"></div>

        <!-- Composer placeholder -->
        <div class="shimmer h-4 w-1/2 rounded bg-yellow-200"></div>

        <!-- Tags placeholder -->
        <div class="flex gap-2">
          <div class="shimmer h-6 w-16 rounded-full bg-yellow-200"></div>
          <div class="shimmer h-6 w-20 rounded-full bg-yellow-200"></div>
        </div>

        <!-- Description placeholder -->
        <div class="space-y-2">
          <div class="shimmer h-3 w-full rounded bg-yellow-200"></div>
          <div class="shimmer h-3 w-5/6 rounded bg-yellow-200"></div>
        </div>

        <!-- Button placeholder -->
        <div class="shimmer mt-4 h-10 w-full rounded bg-yellow-200"></div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
// Default to 6 skeleton cards
withDefaults(defineProps<{ count?: number }>(), {
  count: 6,
});
</script>

<style scoped>
@keyframes shimmer {
  0% {
    background-position: -1000px 0;
  }
  100% {
    background-position: 1000px 0;
  }
}

.shimmer {
  background: linear-gradient(to right, #fefce8 0%, #fde68a 20%, #fffbea 40%, #fefce8 100%);
  background-size: 1000px 100%;
  animation: shimmer 2s infinite linear;
}
</style>
