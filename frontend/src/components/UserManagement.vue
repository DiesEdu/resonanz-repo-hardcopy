<template>
  <section class="rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm">
    <div class="mb-5 flex items-center justify-between">
      <div>
        <h2 class="text-xl font-semibold text-amber-900">User Management</h2>
        <p class="text-sm text-amber-700">Create, update, and remove users</p>
      </div>
      <button
        class="rounded-lg bg-amber-700 px-3 py-2 text-sm font-medium text-white transition hover:bg-amber-800"
        @click="resetForm"
      >
        New User
      </button>
    </div>

    <p v-if="errorMessage" class="mb-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
      {{ errorMessage }}
    </p>

    <form class="mb-6 grid gap-3 md:grid-cols-4" @submit.prevent="submitForm">
      <input
        v-model="form.name"
        type="text"
        placeholder="Name (optional)"
        class="rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
      />
      <input
        v-model="form.email"
        type="email"
        placeholder="Email"
        required
        class="rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
      />
      <select
        v-model="form.role"
        required
        class="rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
      >
        <option value="member">Member</option>
        <option value="librarian">Librarian</option>
        <option value="admin">Admin</option>
      </select>
      <input
        v-model="form.password"
        type="password"
        :placeholder="editingUserId ? 'Password (optional)' : 'Password'"
        :required="!editingUserId"
        minlength="6"
        class="rounded-lg border border-amber-200 px-3 py-2 text-sm text-amber-900 outline-none focus:border-amber-500"
      />
      <div class="md:col-span-4 flex gap-2">
        <button
          type="submit"
          :disabled="isSubmitting"
          class="rounded-lg bg-gradient-to-r from-amber-700 to-yellow-500 px-4 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-60"
        >
          {{ editingUserId ? 'Update User' : 'Create User' }}
        </button>
        <button
          v-if="editingUserId"
          type="button"
          class="rounded-lg border border-amber-300 px-4 py-2 text-sm font-medium text-amber-800"
          @click="resetForm"
        >
          Cancel
        </button>
      </div>
    </form>

    <div v-if="isLoading" class="text-sm text-amber-700">Loading users...</div>
    <div v-else-if="users.length === 0" class="text-sm text-amber-700">No users found.</div>

    <div v-else class="overflow-x-auto">
      <table class="w-full min-w-[640px] text-left text-sm">
        <thead>
          <tr class="border-b border-amber-200 text-amber-700">
            <th class="py-2 pr-3">Name</th>
            <th class="py-2 pr-3">Email</th>
            <th class="py-2 pr-3">Role</th>
            <th class="py-2 pr-3">Created</th>
            <th class="py-2 pr-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id" class="border-b border-amber-100 text-amber-900">
            <td class="py-2 pr-3">{{ user.name || '-' }}</td>
            <td class="py-2 pr-3">{{ user.email }}</td>
            <td class="py-2 pr-3">{{ roleLabel(user.role) }}</td>
            <td class="py-2 pr-3">{{ formatDate(user.created_at) }}</td>
            <td class="py-2 pr-3">
              <div class="flex gap-2">
                <button class="text-amber-800 hover:underline" @click="startEdit(user)">Edit</button>
                <button class="text-red-700 hover:underline" @click="removeUser(user.id)">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import type { User, UserRole } from "@/types";
import LibraryAPIService from "@/services/libraryAPI";

const users = ref<User[]>([]);
const isLoading = ref(false);
const isSubmitting = ref(false);
const errorMessage = ref("");
const editingUserId = ref<number | null>(null);

const form = ref<{
  name: string;
  email: string;
  role: UserRole;
  password: string;
}>({
  name: "",
  email: "",
  role: "member",
  password: "",
});

const roleLabel = (role: UserRole) => {
  switch (role) {
    case "admin":
      return "Admin";
    case "librarian":
      return "Librarian";
    default:
      return "Member";
  }
};

const formatDate = (value: string) => {
  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return value;
  }

  return parsed.toLocaleString();
};

const loadUsers = async () => {
  isLoading.value = true;
  errorMessage.value = "";

  try {
    const response = await LibraryAPIService.getUsers();
    users.value = response.data;
  } catch (error) {
    const message = error instanceof Error ? error.message : "Failed to load users";
    errorMessage.value = message;
  } finally {
    isLoading.value = false;
  }
};

const resetForm = () => {
  editingUserId.value = null;
  form.value = {
    name: "",
    email: "",
    role: "member",
    password: "",
  };
};

const startEdit = (user: User) => {
  editingUserId.value = user.id;
  form.value = {
    name: user.name ?? "",
    email: user.email,
    role: user.role,
    password: "",
  };
};

const submitForm = async () => {
  isSubmitting.value = true;
  errorMessage.value = "";

  try {
    if (editingUserId.value) {
      const payload: { name?: string; email?: string; role?: UserRole; password?: string } = {
        name: form.value.name.trim(),
        email: form.value.email.trim(),
        role: form.value.role,
      };

      if (form.value.password.trim() !== "") {
        payload.password = form.value.password;
      }

      await LibraryAPIService.updateUser(editingUserId.value, payload);
    } else {
      await LibraryAPIService.createUser({
        name: form.value.name.trim(),
        email: form.value.email.trim(),
        role: form.value.role,
        password: form.value.password,
      });
    }

    await loadUsers();
    resetForm();
  } catch (error) {
    const message = error instanceof Error ? error.message : "Failed to save user";
    errorMessage.value = message;
  } finally {
    isSubmitting.value = false;
  }
};

const removeUser = async (userId: number) => {
  if (!window.confirm("Delete this user?")) {
    return;
  }

  errorMessage.value = "";

  try {
    await LibraryAPIService.deleteUser(userId);
    users.value = users.value.filter((user) => user.id !== userId);
    if (editingUserId.value === userId) {
      resetForm();
    }
  } catch (error) {
    const message = error instanceof Error ? error.message : "Failed to delete user";
    errorMessage.value = message;
  }
};

onMounted(async () => {
  await loadUsers();
});
</script>
