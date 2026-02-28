<template>
  <Transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
      @click="closeModal"
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
          v-if="show"
          class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-xl bg-white"
          @click.stop
        >
          <!-- Header -->
          <div class="rounded-t-xl bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <div class="rounded-lg bg-white/20 p-2">
                  <MusicalNoteIcon class="h-6 w-6 text-white" />
                </div>
                <h2 class="text-xl font-bold text-white">Add New Sheet Music</h2>
              </div>
              <button class="text-white/80 transition-colors hover:text-white" @click="closeModal">
                <XMarkIcon class="h-6 w-6" />
              </button>
            </div>
          </div>

          <!-- Form -->
          <form @submit.prevent="handleSubmit" class="space-y-6 p-6">
            <!-- Title -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Title <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.title"
                type="text"
                required
                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                :class="{ 'border-red-500': errors.title }"
                placeholder="e.g., Moonlight Sonata"
              />
              <p v-if="errors.title" class="text-sm text-red-600">{{ errors.title }}</p>
            </div>

            <!-- Composer -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Composer <span class="text-red-500">*</span>
              </label>
              <input
                v-model="formData.composer"
                type="text"
                required
                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                :class="{ 'border-red-500': errors.composer }"
                placeholder="e.g., Ludwig van Beethoven"
              />
              <p v-if="errors.composer" class="text-sm text-red-600">{{ errors.composer }}</p>
            </div>

            <!-- Genre and Difficulty Row -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                  Genre <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="formData.genre"
                  required
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                  :class="{ 'border-red-500': errors.genre }"
                >
                  <option value="">Select genre</option>
                  <option v-for="genre in genreOptions" :key="genre" :value="genre">
                    {{ genre }}
                  </option>
                </select>
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                  Difficulty <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="formData.difficulty"
                  required
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                  :class="{ 'border-red-500': errors.difficulty }"
                >
                  <option value="">Select difficulty</option>
                  <option v-for="level in difficultyOptions" :key="level" :value="level">
                    {{ level }}
                  </option>
                </select>
              </div>
            </div>

            <!-- Pages and Instrumentation Row -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">
                  Pages <span class="text-red-500">*</span>
                </label>
                <input
                  v-model.number="formData.pages"
                  type="number"
                  required
                  min="1"
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                  :class="{ 'border-red-500': errors.pages }"
                />
              </div>

              <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700"> Instrumentation </label>
                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="instrument in instrumentOptions"
                    :key="instrument"
                    type="button"
                    @click="toggleInstrument(instrument)"
                    class="rounded-full px-3 py-1 text-sm font-medium transition-all duration-200"
                    :class="
                      formData.instruments.includes(instrument)
                        ? 'scale-105 bg-indigo-600 text-white shadow-md'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    "
                  >
                    {{ instrument }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700"> Description </label>
              <textarea
                v-model="formData.description"
                rows="4"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                placeholder="Enter a description of the piece..."
              ></textarea>
            </div>

            <!-- Cover Image URL -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700"> Cover Image URL </label>
              <input
                v-model="formData.coverImage"
                type="url"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                placeholder="https://example.com/image.jpg"
              />
              <p class="text-xs text-gray-500">Leave empty to use default image</p>
            </div>

            <!-- Preview Section (if image URL is provided) -->
            <div v-if="formData.coverImage" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">Preview</label>
              <div class="relative h-32 w-32 overflow-hidden rounded-lg border-2 border-gray-200">
                <img
                  :src="formData.coverImage"
                  alt="Cover preview"
                  class="h-full w-full object-cover"
                  @error="handleImageError"
                />
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end gap-3 border-t pt-4">
              <button
                type="button"
                class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700 transition-all duration-200 hover:scale-105 hover:bg-gray-200"
                @click="closeModal"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-white transition-all duration-200 hover:scale-105 hover:bg-indigo-700"
                :disabled="isSubmitting"
              >
                <ArrowPathIcon v-if="isSubmitting" class="h-4 w-4 animate-spin" />
                <span>{{ isSubmitting ? "Adding..." : "Add Sheet Music" }}</span>
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { MusicalNoteIcon, XMarkIcon, ArrowPathIcon } from "@heroicons/vue/24/outline";
import type { SheetMusic } from "@/types";

defineProps<{
  show: boolean;
}>();

const emit = defineEmits(["close", "add"]);

// Form data
const formData = reactive({
  title: "",
  composer: "",
  genre: "",
  difficulty: "",
  pages: 1,
  description: "",
  coverImage: "",
  instruments: [] as string[],
});

// Options
const genreOptions = [
  "Classical",
  "Baroque",
  "Romantic",
  "Impressionist",
  "Modern",
  "Jazz",
  "Contemporary",
];
const difficultyOptions = ["Beginner", "Intermediate", "Advanced"];
const instrumentOptions = [
  "Piano",
  "Violin",
  "Cello",
  "Flute",
  "Guitar",
  "Voice",
  "Orchestra",
  "String Ensemble",
];

// Validation errors
const errors = ref<Record<string, string>>({});

// Submission state
const isSubmitting = ref(false);

// Toggle instrument selection
const toggleInstrument = (instrument: string) => {
  const index = formData.instruments.indexOf(instrument);
  if (index === -1) {
    formData.instruments.push(instrument);
  } else {
    formData.instruments.splice(index, 1);
  }
};

// Handle image error
const handleImageError = (e: Event) => {
  (e.target as HTMLImageElement).src =
    "https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&q=80&w=300&h=400";
};

// Validate form
const validateForm = () => {
  const newErrors: Record<string, string> = {};

  if (!formData.title.trim()) newErrors.title = "Title is required";
  if (!formData.composer.trim()) newErrors.composer = "Composer is required";
  if (!formData.genre) newErrors.genre = "Genre is required";
  if (!formData.difficulty) newErrors.difficulty = "Difficulty is required";
  if (formData.pages < 1) newErrors.pages = "Pages must be at least 1";

  errors.value = newErrors;
  return Object.keys(newErrors).length === 0;
};

// Handle form submission
const handleSubmit = async () => {
  if (!validateForm()) return;

  isSubmitting.value = true;

  // Simulate API call
  await new Promise((resolve) => setTimeout(resolve, 1500));

  // Create new sheet music object
  const newSheetMusic: SheetMusic = {
    id: Date.now(),
    title: formData.title,
    composer: formData.composer,
    genre: formData.genre,
    difficulty: formData.difficulty as "Beginner" | "Intermediate" | "Advanced",
    pages: formData.pages,
    description: formData.description || "No description provided.",
    coverImage:
      formData.coverImage ||
      "https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&q=80&w=300&h=400",
    fileUrl: "/sheets/placeholder.pdf",
    uploadDate: new Date().toISOString().split("T")[0] || new Date().toISOString().split("T")[0]!,
    instruments: formData.instruments.length ? formData.instruments : ["Piano"],
  };

  // Emit the new sheet music
  emit("add", newSheetMusic);

  // Reset form and close modal
  resetForm();
  isSubmitting.value = false;
  emit("close");
};

// Reset form
const resetForm = () => {
  formData.title = "";
  formData.composer = "";
  formData.genre = "";
  formData.difficulty = "";
  formData.pages = 1;
  formData.description = "";
  formData.coverImage = "";
  formData.instruments = [];
  errors.value = {};
};

// Close modal
const closeModal = () => {
  resetForm();
  emit("close");
};
</script>
