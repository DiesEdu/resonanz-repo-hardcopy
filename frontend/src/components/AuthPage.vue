<template>
  <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-yellow-50 via-white to-yellow-100/40 px-4 py-10">
    <div class="pointer-events-none absolute inset-0">
      <div class="absolute -top-24 -left-20 h-72 w-72 rounded-full bg-amber-200/35 blur-3xl"></div>
      <div class="absolute -right-16 bottom-0 h-72 w-72 rounded-full bg-yellow-300/25 blur-3xl"></div>
    </div>

    <div class="relative mx-auto w-full max-w-4xl rounded-3xl border border-white/60 bg-white/80 shadow-xl backdrop-blur-sm">
      <div class="grid md:grid-cols-2">
        <section class="p-8 md:p-10">
          <p class="text-sm font-medium tracking-[0.18em] text-amber-700 uppercase">The Resonanz</p>
          <h1 class="mt-3 text-3xl font-bold text-amber-950">Sheet Music Library</h1>
          <p class="mt-3 text-sm leading-6 text-amber-800/90">
            Sign in to access your collection, manage sheet music, and administer users.
          </p>
        </section>

        <section class="border-t border-amber-100/70 p-8 md:border-t-0 md:border-l md:p-10">
          <div class="mb-5 inline-flex rounded-lg border border-amber-200 bg-amber-50 p-1">
            <button
              class="rounded-md px-4 py-2 text-sm font-semibold transition"
              :class="mode === 'login' ? 'bg-amber-700 text-white' : 'text-amber-800 hover:bg-amber-100'"
              @click="mode = 'login'"
            >
              Login
            </button>
            <button
              class="rounded-md px-4 py-2 text-sm font-semibold transition"
              :class="mode === 'register' ? 'bg-amber-700 text-white' : 'text-amber-800 hover:bg-amber-100'"
              @click="mode = 'register'"
            >
              Register
            </button>
          </div>

          <p v-if="errorMessage" class="mb-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
            {{ errorMessage }}
          </p>

          <form class="space-y-3" @submit.prevent="submitAuth">
            <input
              v-if="mode === 'register'"
              v-model="registerName"
              type="text"
              placeholder="Name (optional)"
              class="w-full rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
            />

            <input
              v-model="email"
              type="email"
              placeholder="Email"
              required
              class="w-full rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
            />

            <input
              v-model="password"
              type="password"
              placeholder="Password"
              required
              minlength="6"
              class="w-full rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
            />

            <button
              type="submit"
              :disabled="isSubmitting"
              class="w-full rounded-lg bg-gradient-to-r from-amber-700 to-yellow-500 px-4 py-2 text-sm font-semibold text-white transition hover:brightness-105 disabled:cursor-not-allowed disabled:opacity-70"
            >
              {{ isSubmitting ? 'Please wait...' : mode === 'login' ? 'Login' : 'Create Account' }}
            </button>
          </form>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import type { User } from "@/types";
import LibraryAPIService from "@/services/libraryAPI";

const emit = defineEmits<{
  authenticated: [user: User];
}>();

const mode = ref<"login" | "register">("login");
const registerName = ref("");
const email = ref("");
const password = ref("");
const isSubmitting = ref(false);
const errorMessage = ref("");

const resetError = () => {
  errorMessage.value = "";
};

const submitAuth = async () => {
  isSubmitting.value = true;
  resetError();

  try {
    if (mode.value === "login") {
      const response = await LibraryAPIService.login({
        email: email.value.trim(),
        password: password.value,
      });
      emit("authenticated", response.data);
      return;
    }

    const response = await LibraryAPIService.register({
      name: registerName.value.trim(),
      email: email.value.trim(),
      password: password.value,
    });

    emit("authenticated", response.data);
  } catch (error) {
    const message = error instanceof Error ? error.message : "Authentication failed";
    errorMessage.value = message;
  } finally {
    isSubmitting.value = false;
  }
};
</script>
