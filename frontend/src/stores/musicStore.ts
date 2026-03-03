import { defineStore } from "pinia";
import { ref, computed, onMounted } from "vue";
import type { SheetMusic } from "@/types";
import { mockSheetMusic } from "@/data/mockData";
import LibraryAPIService from "@/services/libraryAPI";

export const useMusicStore = defineStore("music", () => {
  const sheetMusic = ref<SheetMusic[]>(mockSheetMusic);
  const searchQuery = ref("");
  const selectedGenre = ref("All");
  const selectedDifficulty = ref("All");

  onMounted(async () => {
    // In a real app, you would fetch this data from an API
    // For now, we use mock data defined in mockData.ts
    const fetchScore = (await LibraryAPIService.getScores()) as { data: SheetMusic[] };
    sheetMusic.value = fetchScore.data;
  });

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
        return "bg-yellow-100 text-amber-900";
      case "Intermediate":
        return "bg-yellow-100 text-yellow-800";
      case "Advanced":
        return "bg-yellow-100 text-amber-900";
      default:
        return "bg-yellow-100 text-amber-900";
    }
  };

  // Add new sheet music
  const addSheetMusic = async (newSheet: SheetMusic) => {
    const response = await LibraryAPIService.createScore(newSheet);
    const insertNewScore = response.data as SheetMusic;
    sheetMusic.value = [insertNewScore, ...sheetMusic.value];
  };

  // Update existing sheet music
  const updateSheetMusic = async (updatedSheet: SheetMusic) => {
    await LibraryAPIService.updateScore(updatedSheet.id, updatedSheet);
    sheetMusic.value = sheetMusic.value.map((item) =>
      item.id === updatedSheet.id ? { ...item, ...updatedSheet } : item,
    );
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
    updateSheetMusic,
    availableGenres,
  };
});
