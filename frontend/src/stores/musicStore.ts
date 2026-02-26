import { defineStore } from "pinia";
import { ref, computed } from "vue";
import type { SheetMusic } from "@/types";
import { mockSheetMusic } from "@/data/mockData";

export const useMusicStore = defineStore("music", () => {
  const sheetMusic = ref<SheetMusic[]>(mockSheetMusic);
  const searchQuery = ref("");
  const selectedGenre = ref("All");
  const selectedDifficulty = ref("All");

  const filteredSheetMusic = computed(() => {
    return sheetMusic.value.filter((item) => {
      const matchesSearch =
        item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.composer.toLowerCase().includes(searchQuery.value.toLowerCase());
      const matchesGenre = selectedGenre.value === "All" || item.genre === selectedGenre.value;
      const matchesDifficulty =
        selectedDifficulty.value === "All" || item.difficulty === selectedDifficulty.value;

      return matchesSearch && matchesGenre && matchesDifficulty;
    });
  });

  const getDifficultyColor = (difficulty: string) => {
    switch (difficulty) {
      case "Beginner":
        return "bg-green-100 text-green-800";
      case "Intermediate":
        return "bg-yellow-100 text-yellow-800";
      case "Advanced":
        return "bg-red-100 text-red-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  // Add new sheet music
  const addSheetMusic = (newSheet: SheetMusic) => {
    sheetMusic.value = [newSheet, ...sheetMusic.value];
  };

  // Get unique genres for filter
  const availableGenres = computed(() => {
    const genres = ["All", ...new Set(sheetMusic.value.map((item) => item.genre))];
    return genres;
  });

  return {
    sheetMusic,
    searchQuery,
    selectedGenre,
    selectedDifficulty,
    filteredSheetMusic,
    getDifficultyColor,
    addSheetMusic,
    availableGenres,
  };
});
