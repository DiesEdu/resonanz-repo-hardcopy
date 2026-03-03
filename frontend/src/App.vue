<template>
  <div class="app-shell min-h-screen bg-gradient-to-br from-yellow-50 via-white to-yellow-100/40">
    <!-- Animated background particles -->
    <div class="pointer-events-none fixed inset-0 overflow-hidden">
      <div
        v-for="i in 20"
        :key="i"
        class="animate-float absolute rounded-full bg-yellow-200/20"
        :style="{
          width: `${Math.random() * 300 + 50}px`,
          height: `${Math.random() * 300 + 50}px`,
          left: `${Math.random() * 100}%`,
          top: `${Math.random() * 100}%`,
          animationDelay: `${Math.random() * 5}s`,
          animationDuration: `${Math.random() * 10 + 10}s`,
        }"
      ></div>
    </div>

    <!-- Header with animation -->
    <Transition
      appear
      enter-active-class="transition duration-700 ease-out"
      enter-from-class="opacity-0 -translate-y-full"
      enter-to-class="opacity-100 translate-y-0"
    >
      <header class="sticky top-0 z-40 bg-white/80 shadow-sm backdrop-blur-md">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between">
            <div class="group flex cursor-pointer items-center gap-3" @click="resetFilters">
              <div class="relative">
                <MusicalNoteIcon
                  class="h-8 w-8 text-amber-700 transition-transform duration-300 group-hover:rotate-12"
                />
                <div
                  class="absolute inset-0 rounded-full bg-yellow-300 opacity-0 blur-lg transition-opacity duration-300 group-hover:opacity-50"
                ></div>
              </div>
              <h1
                class="bg-gradient-to-r from-amber-700 to-yellow-500 bg-clip-text text-2xl font-bold text-amber-950 text-transparent"
              >
                Sheet Music Library
              </h1>
            </div>

            <div class="flex items-center gap-4">
              <!-- Add New Button -->
              <Transition
                appear
                enter-active-class="transition duration-500 delay-200"
                enter-from-class="opacity-0 scale-0"
                enter-to-class="opacity-100 scale-100"
              >
                <button class="group relative" @click="openAddModal">
                  <div
                    class="absolute inset-0 rounded-full bg-yellow-300 opacity-0 blur-lg transition-opacity duration-300 group-hover:opacity-50"
                  ></div>
                  <div
                    class="relative flex items-center gap-2 rounded-lg bg-gradient-to-r from-amber-700 to-yellow-500 px-4 py-2 text-white transition-all duration-300 hover:scale-105 hover:shadow-lg"
                  >
                    <PlusIcon class="h-5 w-5" />
                    <span class="hidden sm:inline">Add New</span>
                  </div>
                </button>
              </Transition>

              <!-- Theme toggle -->
              <Transition
                appear
                enter-active-class="transition duration-500 delay-250"
                enter-from-class="opacity-0 scale-0"
                enter-to-class="opacity-100 scale-100"
              >
                <button
                  class="rounded-full p-2 transition-all duration-300 hover:scale-105 hover:bg-yellow-100 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:outline-none"
                  @click="toggleTheme"
                  :title="isDarkTheme ? 'Switch to bright theme' : 'Switch to dark theme'"
                >
                  <SunIcon v-if="isDarkTheme" class="h-5 w-5 text-amber-700" />
                  <MoonIcon v-else class="h-5 w-5 text-amber-700" />
                </button>
              </Transition>

              <!-- Counter animation -->
              <Transition
                appear
                enter-active-class="transition duration-500 delay-300"
                enter-from-class="opacity-0 translate-x-4"
                enter-to-class="opacity-100 translate-x-0"
              >
                <p class="hidden text-amber-700 md:block">
                  <span class="font-semibold text-amber-700">{{ filteredCount }}</span> pieces
                  available
                </p>
              </Transition>

              <!-- Refresh button with animation -->
              <Transition
                appear
                enter-active-class="transition duration-500 delay-400"
                enter-from-class="opacity-0 scale-0"
                enter-to-class="opacity-100 scale-100"
              >
                <button
                  class="rounded-full p-2 transition-all duration-300 hover:rotate-180 hover:bg-yellow-100 focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 focus:outline-none"
                  @click="refreshLibrary"
                  :class="{ 'animate-spin cursor-not-allowed': isRefreshing }"
                  :disabled="isRefreshing"
                  :title="isRefreshing ? 'Refreshing...' : 'Refresh library'"
                >
                  <ArrowPathIcon
                    class="h-5 w-5 transition-colors"
                    :class="isRefreshing ? 'text-amber-700' : 'text-amber-700'"
                  />
                </button>
              </Transition>
            </div>
          </div>

          <!-- Mobile counter -->
          <div class="mt-2 text-center md:hidden">
            <p class="text-sm text-amber-700">
              <span class="font-semibold text-amber-700">{{ filteredCount }}</span> pieces
              available
            </p>
          </div>
        </div>
      </header>
    </Transition>

    <!-- Main Content -->
    <main class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <!-- Search Bar with animation -->
      <Transition
        appear
        enter-active-class="transition duration-500 delay-100"
        enter-from-class="opacity-0 translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
      >
        <div class="mb-8">
          <SearchBar />
        </div>
      </Transition>

      <div class="flex flex-col gap-8 lg:flex-row">
        <!-- Filters Sidebar with animations -->
        <Transition
          appear
          enter-active-class="transition duration-500 delay-200"
          enter-from-class="opacity-0 -translate-x-4"
          enter-to-class="opacity-100 translate-x-0"
        >
          <aside class="space-y-6 lg:w-64">
            <div
              class="rounded-lg bg-white/80 p-4 shadow-lg backdrop-blur-sm transition-shadow duration-300 hover:shadow-xl"
            >
              <GenreFilter />
            </div>
            <div
              class="rounded-lg bg-white/80 p-4 shadow-lg backdrop-blur-sm transition-shadow duration-300 hover:shadow-xl"
            >
              <DifficultyFilter />
            </div>
          </aside>
        </Transition>

        <!-- Music Grid -->
        <div class="flex-1">
          <!-- Loading state -->
          <div v-if="isLoading" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <LoadingSkeleton :count="6" />
          </div>

          <!-- Content with staggered animations -->
          <TransitionGroup
            v-else-if="filteredSheetMusic.length > 0"
            tag="div"
            class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3"
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="opacity-0 scale-90 translate-y-4"
            enter-to-class="opacity-100 scale-100 translate-y-0"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="opacity-100 scale-100 translate-y-0"
            leave-to-class="opacity-0 scale-90 translate-y-4"
            move-class="transition duration-500"
          >
            <MusicCard
              v-for="(music, index) in filteredSheetMusic"
              :key="music.id"
              :music="music"
              :style="{ animationDelay: `${index * 100}ms` }"
              @edit="handleEditRequest"
            />
          </TransitionGroup>

          <!-- Empty state with animation -->
          <Transition
            enter-active-class="transition duration-500"
            enter-from-class="opacity-0 scale-90"
            enter-to-class="opacity-100 scale-100"
          >
            <div
              v-if="filteredSheetMusic.length === 0"
              class="rounded-2xl bg-white/80 py-12 text-center backdrop-blur-sm"
            >
              <div class="relative inline-block">
                <MusicalNoteIcon class="mx-auto mb-4 h-16 w-16 animate-bounce text-yellow-300" />
                <div
                  class="absolute inset-0 animate-pulse rounded-full bg-yellow-200 opacity-30 blur-xl"
                ></div>
              </div>
              <h3 class="mb-2 text-lg font-medium text-amber-950">No sheet music found</h3>
              <p class="mb-4 text-amber-700">Try adjusting your search or filters</p>
              <button
                class="inline-flex items-center gap-2 font-medium text-amber-700 transition-all duration-300 hover:scale-105 hover:text-amber-800"
                @click="resetFilters"
              >
                <ArrowPathIcon class="h-4 w-4" />
                Reset all filters
              </button>
            </div>
          </Transition>
        </div>
      </div>
    </main>

    <!-- Floating action button with animation -->
    <Transition
      enter-active-class="transition duration-300"
      enter-from-class="opacity-0 scale-0"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-0"
    >
      <button
        v-if="showScrollTop"
        class="group fixed right-8 bottom-8 z-50 rounded-full bg-amber-700 p-3 text-white shadow-lg transition-all duration-300 hover:scale-110 hover:bg-amber-800"
        @click="scrollToTop"
      >
        <ArrowUpIcon class="h-5 w-5 transition-transform duration-300 group-hover:-translate-y-1" />
      </button>
    </Transition>

    <!-- Add Sheet Music Modal -->
    <AddSheetMusicModal
      :show="showAddModal"
      :edit-sheet="editingSheet"
      @close="handleCloseModal"
      @add="handleSaveSheetMusic"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from "vue";
import {
  MusicalNoteIcon,
  ArrowPathIcon,
  ArrowUpIcon,
  PlusIcon,
  SunIcon,
  MoonIcon,
} from "@heroicons/vue/24/outline";
import { useMusicStore } from "./stores/musicStore";
import { storeToRefs } from "pinia";
import SearchBar from "./components/SearchBar.vue";
import GenreFilter from "./components/GenreFilter.vue";
import DifficultyFilter from "./components/DifficultyFilter.vue";
import MusicCard from "./components/MusicCard.vue";
import LoadingSkeleton from "./components/LoadingSkeleton.vue";
import AddSheetMusicModal from "./components/AddSheetMusicModal.vue";
import type { SheetMusic } from "./types";

const musicStore = useMusicStore();
const { filteredSheetMusic } = storeToRefs(musicStore);
const filteredCount = computed(() => filteredSheetMusic.value.length); // ✅ Correct - gets array length

const isLoading = ref(false);
const isRefreshing = ref(false);
const isDarkTheme = ref(false);
const showScrollTop = ref(false);
const showAddModal = ref(false);
const editingSheet = ref<SheetMusic | null>(null);

const resetFilters = () => {
  musicStore.searchQuery = "";
  musicStore.selectedGenre = "All";
  musicStore.selectedDifficulty = "All";
};

const refreshLibrary = async () => {
  isRefreshing.value = true;
  isLoading.value = true;

  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 1500));

  isRefreshing.value = false;
  isLoading.value = false;
};

const applyTheme = () => {
  document.documentElement.classList.toggle("theme-dark", isDarkTheme.value);
};

const toggleTheme = () => {
  isDarkTheme.value = !isDarkTheme.value;
  localStorage.setItem("sheet-music-theme", isDarkTheme.value ? "dark" : "light");
  applyTheme();
};

const handleSaveSheetMusic = (sheet: SheetMusic) => {
  if (editingSheet.value) {
    console.log("Updating sheet music:", sheet);
    musicStore.updateSheetMusic(sheet);
  } else {
    console.log("Adding new sheet music:", sheet);
    musicStore.addSheetMusic(sheet);
  }
  editingSheet.value = null;
};

const handleEditRequest = (music: SheetMusic) => {
  editingSheet.value = { ...music, instruments: [...music.instruments] };
  showAddModal.value = true;
};

const handleCloseModal = () => {
  showAddModal.value = false;
  editingSheet.value = null;
};

const openAddModal = () => {
  editingSheet.value = null;
  showAddModal.value = true;
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

const handleScroll = () => {
  showScrollTop.value = window.scrollY > 400;
};

onMounted(() => {
  const savedTheme = localStorage.getItem("sheet-music-theme");
  isDarkTheme.value = savedTheme === "dark";
  applyTheme();
  window.addEventListener("scroll", handleScroll);
});

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<style>
@keyframes float {
  0%,
  100% {
    transform: translateY(0) translateX(0);
  }
  25% {
    transform: translateY(-20px) translateX(10px);
  }
  50% {
    transform: translateY(-30px) translateX(-10px);
  }
  75% {
    transform: translateY(-10px) translateX(20px);
  }
}

.animate-float {
  animation: float linear infinite;
}
</style>
