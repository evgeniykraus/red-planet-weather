<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    variant?: 'default' | 'mars' | 'outline';
    size?: 'sm' | 'md' | 'lg';
    disabled?: boolean;
    loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default',
    size: 'md',
    disabled: false,
    loading: false,
});

const emit = defineEmits<{
    click: [event: MouseEvent];
}>();

const buttonClasses = computed(() => {
    const base = 'inline-flex items-center justify-center rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';

    const variants = {
        default: 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
        mars: 'bg-gradient-to-r from-orange-600 to-red-600 text-white hover:from-orange-700 hover:to-red-700 focus:ring-orange-500',
        outline: 'border-2 border-orange-600 text-orange-600 hover:bg-orange-50 dark:hover:bg-orange-950 focus:ring-orange-500',
    };

    const sizes = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-base',
        lg: 'px-6 py-3 text-lg',
    };

    const disabled = props.disabled || props.loading ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';

    return `${base} ${variants[props.variant]} ${sizes[props.size]} ${disabled}`;
});

const handleClick = (event: MouseEvent) => {
    if (!props.disabled && !props.loading) {
        emit('click', event);
    }
};
</script>

<template>
    <button
        :class="buttonClasses"
        :disabled="disabled || loading"
        @click="handleClick"
    >
        <span v-if="loading" class="mr-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
        <slot />
    </button>
</template>
