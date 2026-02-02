<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    modelValue: string | number;
    options: Array<{ value: string | number; label: string }>;
    placeholder?: string;
    disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Выберите вариант',
    disabled: false,
});

const emit = defineEmits<{
    'update:modelValue': [value: string | number];
}>();

const selectClasses = computed(() => {
    const base = 'w-full px-4 py-2 text-base border-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition-all duration-200';
    const colors = 'border-orange-200 bg-white dark:bg-gray-800 dark:border-orange-800 text-gray-900 dark:text-gray-100';
    const disabled = props.disabled ? 'opacity-50 .cursor-not-allowed' : '.cursor-pointer';

    return `${base} ${colors} ${disabled}`;
});

const handleChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const value = target.value;
    emit('update:modelValue', props.options[0]?.value && typeof props.options[0].value === 'number' ? Number(value) : value);
};
</script>

<template>
    <select
        :class="selectClasses"
        :value="modelValue"
        :disabled="disabled"
        @change="handleChange"
    >
        <option value="" disabled>{{ placeholder }}</option>
        <option
            v-for="option in options"
            :key="option.value"
            :value="option.value"
        >
            {{ option.label }}
        </option>
    </select>
</template>
