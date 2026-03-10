<template>
  <div class="space-y-2">
    <h3 class="mb-3 font-semibold text-amber-800">Genres</h3>
    <button
      v-for="genre in genres"
      :key="genre.id"
      @click="selectedGenre = genre.name"
      class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left transition-colors"
      :class="selectedGenre === genre.name ? 'bg-yellow-100 text-amber-800' : 'hover:bg-yellow-100'"
    >
      <span>{{ genre.name }}</span>
      <span class="text-sm text-amber-600">{{ genre.count }}</span>
    </button>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useMusicStore } from "@/stores/musicStore";
import type { Genre } from "@/types";

const musicStore = useMusicStore();
const selectedGenre = computed({
  get: () => musicStore.selectedGenre,
  set: (value) => (musicStore.selectedGenre = value),
});

const genres = computed<Genre[]>(() => [
  { id: 1, name: "All", count: musicStore.sheetMusic.length },
  {
    id: 2,
    name: "Classical",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Classical").length,
  },
  {
    id: 3,
    name: "Baroque",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Baroque").length,
  },
  {
    id: 4,
    name: "Romantic",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Romantic").length,
  },
  {
    id: 5,
    name: "Impressionist",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Impressionist").length,
  },
  {
    id: 6,
    name: "Modern",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Modern").length,
  },
  {
    id: 7,
    name: "Jazz",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Jazz").length,
  },
  {
    id: 8,
    name: "Contemporary",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Contemporary").length,
  },
  {
    id: 9,
    name: "Christmas",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Christmas").length,
  },
  {
    id: 10,
    name: "Pop",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Pop").length,
  },
  {
    id: 11,
    name: "Rock",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Rock").length,
  },
  {
    id: 12,
    name: "Folk",
    count: musicStore.sheetMusic.filter((s) => s.genre === "Folk").length,
  },
]);
</script>
