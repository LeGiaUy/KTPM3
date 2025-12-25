<script setup>
import { computed, ref } from "vue";
import { useRouter } from "vue-router";
import { authService } from "./services/auth.js";

const router = useRouter();

// Force refresh user when profile is updated
const refreshKey = ref(0);
const user = computed(() => {
  // Include refreshKey to force recomputation
  refreshKey.value;
  return authService.getUser();
});

const handleLogout = async () => {
  await authService.logout();
  router.push({ name: "login" });
};

const handleProfileUpdated = () => {
  // Force refresh user data
  refreshKey.value++;
};
</script>

<template>
  <router-view
    v-if="user"
    :user="user"
    @logout="handleLogout"
    @profile-updated="handleProfileUpdated"
  />
  <router-view v-else @logout="handleLogout" />
</template>

<style scoped></style>
