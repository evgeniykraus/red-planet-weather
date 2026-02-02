<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'mars';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
});

const cardClasses = computed(() => {
    const base = 'overflow-hidden rounded-lg shadow-lg transition-all duration-300';

    if (props.variant === 'mars') {
        return `${base} border border-orange-200 dark:border-orange-900`;
    }

    return `${base} border border-gray-200 dark:border-gray-800`;
});
</script>

<template>
    <div :class="cardClasses">
        <slot />
    </div>
</template>
