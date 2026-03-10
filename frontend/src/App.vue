<template>
  <!-- Login Modal Overlay for guests -->
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="showLoginModal"
        class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 px-4 backdrop-blur-sm"
        @click.self="showLoginModal = false"
      >
        <div class="relative w-full max-w-4xl">
          <button
            class="absolute -top-4 -right-4 z-10 rounded-full bg-white p-2 text-amber-800 shadow-md hover:bg-amber-50"
            @click="showLoginModal = false"
          >
            <XMarkIcon class="h-5 w-5" />
          </button>
          <AuthPage @authenticated="handleAuthenticated" />
        </div>
      </div>
    </Transition>
  </Teleport>

  <div class="app-shell min-h-screen bg-gradient-to-br from-yellow-50 via-white to-yellow-100/40">
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
                Resonanz Library
              </h1>
            </div>

            <div class="flex items-center gap-4">
              <Transition
                appear
                enter-active-class="transition duration-500 delay-200"
                enter-from-class="opacity-0 scale-0"
                enter-to-class="opacity-100 scale-100"
              >
                <button v-if="canEditSheetMusic" class="group relative" @click="openAddModal">
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

              <template v-if="isAuthenticated">
                <div
                  class="hidden rounded-full px-3 py-1 text-xs text-amber-800 md:flex md:items-center md:gap-2"
                >
                  <span class="font-semibold text-amber-800 uppercase">{{
                    currentUser?.role
                  }}</span>
                  <span>{{ currentUser?.email }}</span>
                </div>

                <button
                  class="rounded-lg border border-amber-300 px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100"
                  @click="handleLogout"
                >
                  Logout
                </button>
              </template>

              <button
                v-else
                class="rounded-lg border border-amber-300 bg-white px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100"
                @click="showLoginModal = true"
              >
                Login
              </button>

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

          <div class="mt-2 text-center md:hidden">
            <p class="text-sm text-amber-700">
              <span class="font-semibold text-amber-700">{{ filteredCount }}</span> pieces available
            </p>
          </div>
        </div>
      </header>
    </Transition>

    <main class="relative z-10 mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
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

        <div class="flex-1">
          <div v-if="isLoading" class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <LoadingSkeleton :count="6" />
          </div>

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
              v-for="(music, index) in paginatedSheetMusic"
              :key="music.id"
              :music="music"
              :style="{ animationDelay: `${index * 100}ms` }"
              :can-edit="canEditSheetMusic"
              @edit="handleEditRequest"
            />
          </TransitionGroup>

          <div
            v-if="filteredSheetMusic.length > 0"
            class="mt-8 flex flex-col items-center justify-between gap-4 rounded-xl bg-white/80 px-4 py-3 text-sm text-amber-800 shadow-sm backdrop-blur-sm md:flex-row"
          >
            <p>
              Showing
              <span class="font-semibold">{{ currentPageStart }}</span>
              -
              <span class="font-semibold">{{ currentPageEnd }}</span>
              of
              <span class="font-semibold">{{ filteredSheetMusic.length }}</span>
              pieces
            </p>

            <div class="flex items-center gap-2">
              <button
                class="rounded-md border border-amber-300 px-3 py-1.5 font-semibold transition hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="currentPage === 1"
                @click="goToPreviousPage"
              >
                Previous
              </button>
              <span class="min-w-24 text-center font-semibold">
                Page {{ currentPage }} / {{ totalPages }}
              </span>
              <button
                class="rounded-md border border-amber-300 px-3 py-1.5 font-semibold transition hover:bg-amber-100 disabled:cursor-not-allowed disabled:opacity-50"
                :disabled="currentPage === totalPages"
                @click="goToNextPage"
              >
                Next
              </button>
            </div>
          </div>

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

      <div v-if="canManageUsers" class="mt-10">
        <UserManagement />
      </div>
    </main>

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

    <AddSheetMusicModal
      v-if="canEditSheetMusic"
      :show="showAddModal"
      :edit-sheet="editingSheet"
      @close="handleCloseModal"
      @add="handleSaveSheetMusic"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import {
  MusicalNoteIcon,
  ArrowPathIcon,
  ArrowUpIcon,
  PlusIcon,
  SunIcon,
  MoonIcon,
  XMarkIcon,
} from "@heroicons/vue/24/outline";
import { useMusicStore } from "./stores/musicStore";
import { storeToRefs } from "pinia";
import SearchBar from "./components/SearchBar.vue";
import GenreFilter from "./components/GenreFilter.vue";
import DifficultyFilter from "./components/DifficultyFilter.vue";
import MusicCard from "./components/MusicCard.vue";
import LoadingSkeleton from "./components/LoadingSkeleton.vue";
import AddSheetMusicModal from "./components/AddSheetMusicModal.vue";
import UserManagement from "./components/UserManagement.vue";
import AuthPage from "./components/AuthPage.vue";
import type { SheetMusic, User } from "./types";

const AUTH_STORAGE_KEY = "sheet-music-auth-user";

const musicStore = useMusicStore();
const { filteredSheetMusic } = storeToRefs(musicStore);
const filteredCount = computed(() => filteredSheetMusic.value.length);
const ITEMS_PER_PAGE = 9;

const isLoading = ref(false);
const isRefreshing = ref(false);
const isDarkTheme = ref(false);
const showScrollTop = ref(false);
const showAddModal = ref(false);
const editingSheet = ref<SheetMusic | null>(null);
const currentUser = ref<User | null>(null);
const currentPage = ref(1);

const showLoginModal = ref(false);
const isAuthenticated = computed(() => currentUser.value !== null);
const canEditSheetMusic = computed(() => currentUser.value?.role === "admin");
const canManageUsers = computed(
  () => currentUser.value?.role === "admin" || currentUser.value?.role === "librarian",
);
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredSheetMusic.value.length / ITEMS_PER_PAGE)),
);
const paginatedSheetMusic = computed(() => {
  const startIndex = (currentPage.value - 1) * ITEMS_PER_PAGE;
  return filteredSheetMusic.value.slice(startIndex, startIndex + ITEMS_PER_PAGE);
});
const currentPageStart = computed(() =>
  filteredSheetMusic.value.length === 0 ? 0 : (currentPage.value - 1) * ITEMS_PER_PAGE + 1,
);
const currentPageEnd = computed(() =>
  Math.min(currentPage.value * ITEMS_PER_PAGE, filteredSheetMusic.value.length),
);

const goToPreviousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value -= 1;
  }
};

const goToNextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value += 1;
  }
};

const resetFilters = () => {
  musicStore.searchQuery = "";
  musicStore.selectedGenre = "All";
  musicStore.selectedDifficulty = "All";
  currentPage.value = 1;
};

const refreshLibrary = async () => {
  isRefreshing.value = true;
  isLoading.value = true;

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
  if (!canEditSheetMusic.value) return;
  if (editingSheet.value) {
    musicStore.updateSheetMusic(sheet);
  } else {
    musicStore.addSheetMusic(sheet);
  }
  editingSheet.value = null;
};

const handleEditRequest = (music: SheetMusic) => {
  if (!canEditSheetMusic.value) return;
  editingSheet.value = { ...music, instruments: [...music.instruments] };
  showAddModal.value = true;
};

const handleCloseModal = () => {
  showAddModal.value = false;
  editingSheet.value = null;
};

const openAddModal = () => {
  if (!canEditSheetMusic.value) return;
  editingSheet.value = null;
  showAddModal.value = true;
};

const scrollToTop = () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

const handleScroll = () => {
  showScrollTop.value = window.scrollY > 400;
};

const handleAuthenticated = (user: User) => {
  currentUser.value = user;
  localStorage.setItem(AUTH_STORAGE_KEY, JSON.stringify(user));
  showLoginModal.value = false;
};

const handleLogout = () => {
  currentUser.value = null;
  showAddModal.value = false;
  editingSheet.value = null;
  currentPage.value = 1;
  localStorage.removeItem(AUTH_STORAGE_KEY);
};

watch(filteredSheetMusic, (newList) => {
  const latestTotalPages = Math.max(1, Math.ceil(newList.length / ITEMS_PER_PAGE));
  if (currentPage.value > latestTotalPages) {
    currentPage.value = latestTotalPages;
  }
  if (currentPage.value < 1) {
    currentPage.value = 1;
  }
});
onMounted(() => {
  const savedTheme = localStorage.getItem("sheet-music-theme");
  isDarkTheme.value = savedTheme === "dark";
  applyTheme();

  const savedAuth = localStorage.getItem(AUTH_STORAGE_KEY);
  if (savedAuth) {
    try {
      currentUser.value = JSON.parse(savedAuth) as User;
    } catch {
      localStorage.removeItem(AUTH_STORAGE_KEY);
    }
  }

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
