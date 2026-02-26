<template>
  <Transition
    appear
    enter-active-class="transition duration-300 ease-out transform"
    enter-from-class="opacity-0 scale-95 translate-y-4"
    enter-to-class="opacity-100 scale-100 translate-y-0"
    leave-active-class="transition duration-200 ease-in transform"
    leave-from-class="opacity-100 scale-100 translate-y-0"
    leave-to-class="opacity-0 scale-95 translate-y-4"
  >
    <div
      v-if="show"
      :class="['card', customClass]"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      @click="$emit('click')"
    >
      <slot :isHovered="isHovered"></slot>

      <!-- Animated glow effect on hover -->
      <div
        v-if="isHovered && withGlow"
        class="pointer-events-none absolute inset-0 rounded-lg"
        :class="glowClass"
      ></div>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";

const props = defineProps<{
  show?: boolean;
  customClass?: string;
  withGlow?: boolean;
  glowColor?: "indigo" | "blue" | "purple" | "green";
}>();

const isHovered = ref(false);

const glowClass = computed(() => {
  const colors = {
    indigo: "bg-indigo-400/10 shadow-lg shadow-indigo-500/20",
    blue: "bg-blue-400/10 shadow-lg shadow-blue-500/20",
    purple: "bg-purple-400/10 shadow-lg shadow-purple-500/20",
    green: "bg-green-400/10 shadow-lg shadow-green-500/20",
  };
  return colors[props.glowColor || "indigo"];
});
</script>

<style scoped>
.card {
  @apply relative overflow-hidden;
  animation: cardFloat 3s ease-in-out infinite;
}

@keyframes cardFloat {
  0%,
  100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-5px);
  }
}
</style>
