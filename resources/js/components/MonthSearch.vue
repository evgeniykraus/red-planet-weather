<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Button from './ui/Button.vue';
import Select from './ui/Select.vue';
import Card from './ui/Card.vue';
import { Search } from 'lucide-vue-next';

interface Props {
    availableMonths: number[];
    selectedMonth?: number;
}

const props = defineProps<Props>();

const selectedMarsMonth = ref<number>(props.selectedMonth || 0);
const isLoading = ref(false);

const monthOptions = computed(() => {
    return props.availableMonths.map(month => ({
        value: month,
        label: `Месяц ${month}`,
    }));
});

const handleSearch = () => {
    if (!selectedMarsMonth.value || selectedMarsMonth.value < 1 || selectedMarsMonth.value > 12) {
        return;
    }

    isLoading.value = true;

    router.get('/statistics', {
        mars_month: selectedMarsMonth.value,
    }, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
</script>

<template>
    <Card variant="mars">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <Search class="w-6 h-6 mr-2 text-orange-600" />
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Поиск по марсианскому месяцу
                </h2>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Выберите месяц (1-12) для просмотра статистики температур и наблюдений
            </p>

            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                    <Select
                        v-model="selectedMarsMonth"
                        :options="monthOptions"
                        placeholder="Выберите марсианский месяц (1-12)"
                        :disabled="isLoading"
                    />
                </div>
                <Button
                    variant="mars"
                    size="md"
                    :loading="isLoading"
                    :disabled="!selectedMarsMonth || isLoading"
                    @click="handleSearch"
                >
                    Получить статистику
                </Button>
            </div>
        </div>
    </Card>
</template>
