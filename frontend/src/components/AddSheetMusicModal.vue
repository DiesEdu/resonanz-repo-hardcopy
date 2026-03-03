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
                <h2 class="text-xl font-bold text-white">
                  {{ isEditMode ? "Edit Sheet Music" : "Add New Sheet Music" }}
                </h2>
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
                  :disabled="ifOrchestraCollections && !ifFullScore"
                  class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                  :class="
                    ifOrchestraCollections && !ifFullScore
                      ? 'bg-gray-100'
                      : 'border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500'
                  "
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

            <div v-if="ifOrchestraCollections || ifFullScore" class="space-y-2">
              <label class="block text-sm font-medium text-gray-700"> Orchestra Collections </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="section in orchestraCollectionSections"
                  :key="section"
                  type="button"
                  @click="openOrchestraPopup(section)"
                  class="rounded-full px-3 py-1 text-sm font-medium transition-all duration-200"
                  :class="
                    selectedOrchestraSection === section
                      ? 'scale-105 bg-indigo-600 text-white shadow-md'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  "
                >
                  {{ section }}
                </button>
              </div>
              <p class="text-xs text-gray-500">
                Choose a section to select one or more instruments.
              </p>
              <div v-if="formData.orchestraCollections.length" class="flex flex-wrap gap-2 pt-1">
                <button
                  v-for="(selected, index) in formData.orchestraCollections"
                  :key="`${selected}-${index}`"
                  type="button"
                  class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700 transition-colors hover:bg-indigo-200"
                  @click="removeOrchestraCollectionValue(index)"
                >
                  {{ selected }}
                </button>
              </div>
            </div>

            <Transition
              enter-active-class="transition duration-200 ease-out"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-active-class="transition duration-150 ease-in"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
            >
              <div
                v-if="showOrchestraPopup"
                class="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4"
                @click.stop="closeOrchestraPopup"
              >
                <div class="w-full max-w-lg rounded-xl bg-white p-5 shadow-xl" @click.stop>
                  <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">
                      {{ selectedOrchestraSection }} Instruments
                    </h3>
                    <button
                      type="button"
                      class="text-sm font-medium text-gray-500 hover:text-gray-700"
                      @click="closeOrchestraPopup"
                    >
                      Close
                    </button>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <button
                      v-for="(instrument, index) in selectedOrchestraValues"
                      :key="`${instrument}-${index}`"
                      type="button"
                      class="rounded-full px-3 py-1 text-sm font-medium transition-all duration-200"
                      :class="
                        hasOrchestraCollectionInstrument(instrument)
                          ? 'scale-105 bg-indigo-600 text-white shadow-md'
                          : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                      "
                      @click="toggleOrchestraCollectionValue(instrument)"
                    >
                      {{ instrument }}
                    </button>
                  </div>
                </div>
              </div>
            </Transition>

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

            <!-- Location File Library -->
            <div class="space-y-2">
              <label class="block text-sm font-medium text-gray-700">
                Location in File Library
              </label>
              <input
                v-model="formData.location_file"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
                placeholder="E-Indonesia"
              />
              <p class="text-xs text-gray-500">Optional: Specify where the file is stored</p>
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
                <span>{{
                  isSubmitting
                    ? isEditMode
                      ? "Updating..."
                      : "Adding..."
                    : isEditMode
                      ? "Update Sheet Music"
                      : "Add Sheet Music"
                }}</span>
              </button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup lang="ts">
import { ref, reactive, computed, watch } from "vue";
import { MusicalNoteIcon, XMarkIcon, ArrowPathIcon } from "@heroicons/vue/24/outline";
import type { SheetMusic } from "@/types";

const props = defineProps<{
  show: boolean;
  editSheet?: SheetMusic | null;
}>();

const emit = defineEmits(["close", "add"]);
const isEditMode = computed(() => !!props.editSheet);
const scoreType = ref("Single");

// Form data
const formData = reactive({
  title: "",
  composer: "",
  genre: "",
  difficulty: "",
  pages: 1,
  description: "",
  location_file: "",
  coverImage: "",
  instruments: [] as string[],
  orchestraCollections: [] as string[],
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
  "Christmas",
  "Pop",
];
const difficultyOptions = ["Beginner", "Intermediate", "Advanced"];
const instrumentOptions = [
  "Piano",
  "Choir",
  "Violin",
  "Cello",
  "Flute",
  "Guitar",
  "Voice",
  "Orchestra",
  "String Ensemble",
  "Orchestra Collections",
  "Full Score",
];
const ifOrchestraCollections = ref(false);
const ifFullScore = ref(false);
const orchestraCollectionSections = [
  "Woodwinds",
  "Brass",
  "Percussion",
  "Strings",
  "Keyboard",
  "Timpani",
  "Drum Set",
] as const;
const woodwinds = [
  "Piccolo",
  "Flute",
  "Oboe",
  "English Horn",
  "Clarinet",
  "Bass Clarinet",
  "Bassoon",
];
const brass = ["Trumpet", "French Horn", "Trombone", "Tuba"];
const percussion = [
  "Snare Drum",
  "Bass Drum",
  "Cymbals",
  "Triangle",
  "Xylophone",
  "Marimba",
  "Glockenspiel",
  "Vibraphone",
];
const strings = ["Violin", "Violin", "Viola", "Cello", "Contrabass"];
const keyboard = ["Piano", "Harpsichord", "Organ", "Celesta"];
const timpani = ["Timpani"];
const drumSet = ["Drum Set"];

const orchestraCollectionMap = {
  Woodwinds: woodwinds,
  Brass: brass,
  Percussion: percussion,
  Strings: strings,
  Keyboard: keyboard,
  Timpani: timpani,
  "Drum Set": drumSet,
} as const;

type OrchestraSection = (typeof orchestraCollectionSections)[number];

// Validation errors
const errors = ref<Record<string, string>>({});

// Submission state
const isSubmitting = ref(false);
const showOrchestraPopup = ref(false);
const selectedOrchestraSection = ref<OrchestraSection>("Woodwinds");
const selectedOrchestraValues = computed(
  () => orchestraCollectionMap[selectedOrchestraSection.value] ?? [],
);

// Toggle instrument selection
const toggleInstrument = (instrument: string) => {
  const index = formData.instruments.indexOf(instrument);
  if (instrument === "Orchestra Collections" || instrument === "Full Score") {
    //Orchestra Collections
    if (instrument === "Orchestra Collections") {
      scoreType.value = "Orchestra Collections";
      ifOrchestraCollections.value = true;
      ifFullScore.value = false;
      if (formData.instruments.indexOf("Full Score") !== -1) {
        formData.instruments.splice(formData.instruments.indexOf("Full Score"), 1);
      }
    } else if (instrument === "Full Score") {
      scoreType.value = "Full Score";
      ifFullScore.value = true;
      ifOrchestraCollections.value = false;
      if (formData.instruments.indexOf("Orchestra Collections") !== -1) {
        formData.instruments.splice(formData.instruments.indexOf("Orchestra Collections"), 1);
      }
    }
    if (!ifOrchestraCollections.value && !ifFullScore.value) {
      formData.orchestraCollections = [];
      showOrchestraPopup.value = false;
      scoreType.value = "Single";
    } else {
      openOrchestraPopup(selectedOrchestraSection.value);
    }
  }
  if (index === -1) {
    formData.instruments.push(instrument);
  } else {
    formData.instruments.splice(index, 1);
  }
};

const openOrchestraPopup = (section: OrchestraSection) => {
  selectedOrchestraSection.value = section;
  showOrchestraPopup.value = true;
};

const closeOrchestraPopup = () => {
  showOrchestraPopup.value = false;
};

const escapeRegExp = (value: string) => value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");

const getOrchestraCollectionBase = (value: string) => value.replace(/\s\d+$/, "").trim();

const getOrchestraCollectionNumber = (value: string, base: string) => {
  const match = value.match(new RegExp(`^${escapeRegExp(base)}(?:\\s(\\d+))?$`));
  if (!match) return 0;
  return match[1] ? Number.parseInt(match[1], 10) : 1;
};

const hasOrchestraCollectionInstrument = (base: string) =>
  formData.orchestraCollections.some((value) => getOrchestraCollectionNumber(value, base) > 0);

const normalizeOrchestraCollectionInstrument = (base: string) => {
  const matchingIndexes = formData.orchestraCollections.reduce<number[]>(
    (indexes, selected, idx) => {
      if (getOrchestraCollectionNumber(selected, base) > 0) {
        indexes.push(idx);
      }
      return indexes;
    },
    [],
  );

  if (matchingIndexes.length === 1) {
    const index = matchingIndexes[0];
    if (index !== undefined) {
      formData.orchestraCollections[index] = base;
    }
    return;
  }

  if (matchingIndexes.length > 1) {
    matchingIndexes.forEach((idx, order) => {
      formData.orchestraCollections[idx] = `${base} ${order + 1}`;
    });
  }
};

const toggleOrchestraCollectionValue = (value: string) => {
  formData.orchestraCollections.push(value);
  normalizeOrchestraCollectionInstrument(value);
};

const removeOrchestraCollectionValue = (index: number) => {
  const removedValue = formData.orchestraCollections[index];
  if (!removedValue) return;
  const base = getOrchestraCollectionBase(removedValue);
  formData.orchestraCollections.splice(index, 1);
  normalizeOrchestraCollectionInstrument(base);
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

  // Create add/edit payload
  const newSheetMusic: SheetMusic = {
    id: props.editSheet?.id ?? Date.now(),
    title: formData.title,
    composer: formData.composer,
    genre: formData.genre,
    difficulty: formData.difficulty as "Beginner" | "Intermediate" | "Advanced",
    pages: ifOrchestraCollections.value && !ifFullScore.value ? 0 : formData.pages,
    description: formData.description || "No description provided.",
    location_file: formData.location_file || "No location file provided.",
    coverImage:
      formData.coverImage ||
      "https://images.unsplash.com/photo-1520523839897-bd0b52f945a0?auto=format&fit=crop&q=80&w=300&h=400",
    instruments: formData.orchestraCollections.length
      ? formData.orchestraCollections
      : formData.instruments.length
        ? formData.instruments
        : ["Piano"],
    score_type: scoreType.value,
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
  formData.location_file = "";
  formData.coverImage = "";
  formData.instruments = [];
  formData.orchestraCollections = [];
  scoreType.value = "Single";
  ifFullScore.value = false;
  ifOrchestraCollections.value = false;
  showOrchestraPopup.value = false;
  selectedOrchestraSection.value = "Woodwinds";
  errors.value = {};
};

// Close modal
const closeModal = () => {
  resetForm();
  emit("close");
};

const populateForm = (sheet: SheetMusic) => {
  formData.title = sheet.title || "";
  formData.composer = sheet.composer || "";
  formData.genre = sheet.genre || "";
  formData.difficulty = sheet.difficulty || "";
  formData.pages = sheet.pages > 0 ? sheet.pages : 1;
  formData.description = sheet.description || "";
  formData.location_file = sheet.location_file || "";
  formData.coverImage = sheet.coverImage || "";
  formData.instruments =
    sheet.pages === 0 ? ["Orchestra Collections"] : [...(sheet.instruments || [])];
  formData.orchestraCollections =
    sheet.score_type === "Orchestra Collections" || sheet.score_type === "Full Score"
      ? [...(sheet.instruments || [])]
      : [];
  ifOrchestraCollections.value = sheet.score_type === "Orchestra Collections";
  ifFullScore.value = sheet.score_type === "Full Score";
  scoreType.value = sheet.score_type || "Single";
  showOrchestraPopup.value = false;
  selectedOrchestraSection.value = "Woodwinds";
  errors.value = {};
  if (ifFullScore.value) {
    formData.instruments = ["Full Score"];
  }
};

watch(
  () => props.show,
  (isOpen) => {
    if (!isOpen) return;
    if (props.editSheet) {
      populateForm(props.editSheet);
    } else {
      resetForm();
    }
  },
);

watch(
  () => props.editSheet,
  (sheet) => {
    if (!props.show) return;
    if (sheet) {
      populateForm(sheet);
    }
  },
);
</script>
