<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';

interface Props {
    availableMonths: number[];
    selectedMonth?: number;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    loadingChange: [isLoading: boolean];
}>();

const selectedMarsMonth = ref<number>(props.selectedMonth || 0);
const isLoading = ref(false);

const monthOptions = computed(() => {
    return props.availableMonths.map(month => ({
        value: month,
        label: `Месяц ${month}`,
    }));
});

// Синхронизируем selectedMarsMonth с props.selectedMonth
watch(() => props.selectedMonth, (newValue) => {
    if (newValue) {
        selectedMarsMonth.value = newValue;
    }
}, { immediate: true });

const handleSearch = () => {
    if (!selectedMarsMonth.value || selectedMarsMonth.value < 1 || selectedMarsMonth.value > 12) {
        return;
    }

    isLoading.value = true;
    emit('loadingChange', true);

    // Минимальная задержка для показа анимации загрузки
    const minLoadingTime = 2700; // Чуть больше времени анимации (2.7 сек)
    const startTime = Date.now();

    router.get('/', {
        mars_month: selectedMarsMonth.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, minLoadingTime - elapsedTime);
            
            setTimeout(() => {
                isLoading.value = false;
                emit('loadingChange', false);
            }, remainingTime);
        },
    });
};
</script>

<template>
    <div class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-4">
            <Search class="w-6 h-6 text-primary-orange" />
            <h2 class="text-white text-xl font-bold uppercase tracking-wider font-orbitron">
                Поиск данных
            </h2>
        </div>

        <p class="text-text-muted text-sm mb-4">
            Выберите марсианский месяц (1-12) для просмотра погодной статистики
        </p>

        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <select
                    v-model="selectedMarsMonth"
                    :disabled="isLoading"
                    class="w-full bg-deep-background border border-primary-orange/30 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-primary-orange transition-colors disabled:opacity-50 cursor-pointer"
                >
                    <option :value="0" disabled>Выберите месяц...</option>
                    <option
                        v-for="month in availableMonths"
                        :key="month"
                        :value="month"
                    >
                        Месяц {{ month }}
                    </option>
                </select>
            </div>
            <button
                @click="handleSearch"
                :disabled="!selectedMarsMonth || isLoading"
                class="px-6 py-3 bg-primary-orange hover:bg-primary-orange/80 disabled:bg-primary-orange/30 text-white font-bold uppercase tracking-wide rounded-lg transition-all disabled:cursor-not-allowed flex items-center justify-center gap-2 cursor-pointer disabled:cursor-not-allowed"
            >
                <Search v-if="!isLoading" :size="18" />
                <span v-if="isLoading" class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                {{ isLoading ? 'Загрузка...' : 'Найти' }}
            </button>
        </div>
    </div>
</template>
