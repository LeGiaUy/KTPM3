<script setup>
import { computed } from "vue";
import { useRouter } from "vue-router";
import { authService } from "./services/auth.js";

const router = useRouter();

const user = computed(() => authService.getUser());

const handleLogout = async () => {
  await authService.logout();
  router.push({ name: "login" });
};
</script>

<template>
  <router-view v-if="user" :user="user" @logout="handleLogout" />
  <router-view v-else @logout="handleLogout" />
</template>

<style scoped></style>
