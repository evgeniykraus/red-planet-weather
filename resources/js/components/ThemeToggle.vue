<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Sun, Moon } from 'lucide-vue-next';

const isDark = ref(false);

const initTheme = () => {
    // Проверяем localStorage или системные настройки
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
};

const toggleTheme = () => {
    isDark.value = !isDark.value;
    
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
    }
};

onMounted(() => {
    initTheme();
});
</script>

<template>
    <button
        @click="toggleTheme"
        class="p-2 rounded-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 shadow-sm"
        :title="isDark ? 'Переключить на светлую тему' : 'Переключить на темную тему'"
        aria-label="Переключить тему"
    >
        <Sun v-if="isDark" class="w-5 h-5 text-yellow-500" />
        <Moon v-else class="w-5 h-5 text-gray-700 dark:text-gray-300" />
    </button>
</template>
