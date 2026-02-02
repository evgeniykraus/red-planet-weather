<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'spring' | 'summer' | 'autumn' | 'winter' | 'info';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});

const badgeClasses = computed(() => {
    const base = 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium';

    const variants = {
        default: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-100',
        spring: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        summer: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        autumn: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
        winter: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        info: 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
    };

    return `${base} ${variants[props.variant]}`;
});
</script>

<template>
    <span :class="badgeClasses">
        <slot />
    </span>
</template>
