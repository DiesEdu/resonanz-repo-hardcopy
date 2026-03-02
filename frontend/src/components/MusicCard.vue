<template>
  <div>
    <AnimatedCard
      :show="true"
      custom-class="group cursor-pointer"
      with-glow
      glow-color="indigo"
      @click="showDetails = true"
    >
      <template #default="{ isHovered }">
        <div class="relative">
          <!-- Image with zoom effect -->
          <div class="h-48 overflow-hidden">
            <img
              :src="music.coverImage"
              :alt="music.title"
              class="h-full w-full object-cover transition-transform duration-700 ease-out"
              :class="{ 'scale-110': isHovered }"
            />

            <!-- Overlay with animated gradient -->
            <div
              class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent opacity-0 transition-opacity duration-300"
              :class="{ 'opacity-100': isHovered }"
            ></div>
          </div>

          <!-- Floating badge with animation -->
          <div
            class="absolute top-3 right-3 transform rounded-full bg-white/90 px-2 py-1 text-xs font-medium shadow-lg backdrop-blur-sm transition-all duration-300"
            :class="{ '-translate-y-1 scale-110': isHovered }"
          >
            {{ music.pages }} pages
          </div>

          <!-- Content -->
          <div class="p-4">
            <h3
              class="mb-1 text-lg font-semibold transition-colors duration-300"
              :class="{ 'text-indigo-600': isHovered }"
            >
              {{ music.title }}
            </h3>
            <p class="mb-2 text-sm text-gray-600">{{ music.composer }}</p>

            <div class="mb-3 flex flex-wrap gap-2">
              <span
                class="transform rounded-full bg-indigo-100 px-2 py-1 text-xs text-indigo-800 transition-all duration-300"
                :class="{ 'scale-105 bg-indigo-200': isHovered }"
              >
                {{ music.genre }}
              </span>
              <span
                class="transform rounded-full px-2 py-1 text-xs transition-all duration-300"
                :class="[difficultyColor, { 'scale-105': isHovered }]"
              >
                {{ music.difficulty }}
              </span>
            </div>

            <p class="mb-3 line-clamp-2 text-sm text-gray-600">{{ music.description }}</p>

            <div class="flex items-center justify-between text-sm text-gray-500">
              <span class="flex items-center gap-1">
                <DocumentTextIcon class="h-4 w-4" />
                {{ music.instruments.join(", ") }}
              </span>
            </div>

            <!-- Animated download button -->
            <!-- <button
              class="group/btn relative mt-4 flex w-full items-center justify-center gap-2 overflow-hidden rounded-lg bg-indigo-600 py-2 text-white transition-all duration-300 hover:bg-indigo-700"
              @click.stop="handleDownload"
            >
              <span class="relative z-10 flex items-center gap-2">
                <DocumentArrowDownIcon
                  class="h-5 w-5 transition-transform duration-300"
                  :class="{ 'translate-y-1': isDownloading }"
                />
                <span>{{ downloadText }}</span>
              </span> -->

            <!-- Ripple effect -->
            <!-- <span v-if="isDownloading" class="absolute inset-0 bg-indigo-700"></span>
              <span
                v-for="i in 3"
                :key="i"
                class="animate-ripple absolute inset-0 rounded-lg bg-white/20"
                :style="{ animationDelay: `${i * 0.3}s` }"
              ></span>
            </button> -->
          </div>
        </div>
      </template>
    </AnimatedCard>

    <!-- Details Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showDetails"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
        @click="showDetails = false"
      >
        <Transition
          enter-active-class="transition duration-300 ease-out transform"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition duration-200 ease-in transform"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="showDetails"
            class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white"
            @click.stop
          >
            <div class="relative">
              <img
                :src="music.coverImage"
                :alt="music.title"
                class="h-64 w-full rounded-t-xl object-cover"
              />
              <button
                class="absolute top-4 right-4 rounded-full bg-white/90 p-2 backdrop-blur-sm transition-colors hover:bg-white"
                @click="showDetails = false"
              >
                <XMarkIcon class="h-5 w-5" />
              </button>
            </div>

            <div class="p-6">
              <h2 class="mb-2 text-2xl font-bold">{{ music.title }}</h2>
              <p class="mb-4 text-lg text-gray-600">{{ music.composer }}</p>

              <div class="mb-6 flex gap-2">
                <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm text-indigo-800">{{
                  music.genre
                }}</span>
                <span class="rounded-full px-3 py-1 text-sm" :class="difficultyColor">{{
                  music.difficulty
                }}</span>
              </div>

              <p class="mb-6 text-gray-700">{{ music.description }}</p>

              <div class="border-t pt-6">
                <h3 class="mb-3 font-semibold">Details</h3>
                <div class="grid grid-cols-2 gap-4">
                  <div class="flex items-center gap-2 text-gray-600">
                    <DocumentTextIcon class="h-5 w-5" />
                    <span>{{ music.pages === 0 ? "~ pages" : `${music.pages} pages` }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-600">
                    <MusicalNoteIcon class="h-5 w-5" />
                    <span>{{ music.instruments.join(", ") }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-gray-600">
                    <CalendarIcon class="h-5 w-5" />
                    <span>Added {{ formatDate(music.uploadDate) }}</span>
                  </div>
                </div>
              </div>

              <!-- <button
                class="mt-6 flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 py-3 text-lg text-white transition-colors hover:bg-indigo-700"
                @click="handleDownload"
              >
                <DocumentArrowDownIcon class="h-5 w-5" />
                Download Sheet Music
              </button> -->
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import {
  DocumentArrowDownIcon,
  DocumentTextIcon,
  MusicalNoteIcon,
  CalendarIcon,
  XMarkIcon,
} from "@heroicons/vue/24/outline";
import type { SheetMusic } from "@/types";
import { useMusicStore } from "@/stores/musicStore";
import AnimatedCard from "./AnimatedCard.vue";

const props = defineProps<{
  music: SheetMusic;
}>();

const musicStore = useMusicStore();
const showDetails = ref(false);
const isDownloading = ref(false);
const downloadText = ref("Download Sheet Music");

const difficultyColor = computed(() => musicStore.getDifficultyColor(props.music.difficulty));

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};

const handleDownload = (e: Event) => {
  e.stopPropagation();
  isDownloading.value = true;
  downloadText.value = "Downloading...";

  // Simulate download
  setTimeout(() => {
    isDownloading.value = false;
    downloadText.value = "Downloaded!";

    setTimeout(() => {
      downloadText.value = "Download Sheet Music";
    }, 2000);
  }, 1500);
};
</script>

<style scoped>
@keyframes ripple {
  0% {
    transform: scale(0);
    opacity: 0.5;
  }
  100% {
    transform: scale(4);
    opacity: 0;
  }
}

.animate-ripple {
  animation: ripple 1.5s ease-out infinite;
}
</style>
