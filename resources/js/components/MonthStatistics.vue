<script setup lang="ts">
import { computed } from 'vue';
import Card from './ui/Card.vue';
import Badge from './ui/Badge.vue';
import { ThermometerSun, ThermometerSnowflake, TrendingDown, TrendingUp, Calendar, Database } from 'lucide-vue-next';

interface Statistics {
    month: number;
    month_name: string;
    ls_range: string;
    season: string;
    season_description: string;
    statistics: {
        average_temp: number;
        absolute_min: number;
        absolute_max: number;
        total_sols: number;
    };
    date_range: {
        first_date: string;
        last_date: string;
    };
    ls_actual: {
        min: number;
        max: number;
    };
}

interface Props {
    statistics: Statistics;
}

const props = defineProps<Props>();

const seasonVariant = computed(() => {
    const season = props.statistics.season.toLowerCase();
    if (season.includes('весна') || season.includes('spring')) return 'spring';
    if (season.includes('лето') || season.includes('summer')) return 'summer';
    if (season.includes('осень') || season.includes('autumn')) return 'autumn';
    if (season.includes('зима') || season.includes('winter')) return 'winter';
    return 'default';
});

const getTemperatureColor = (temp: number): string => {
    if (temp <= -50) return 'text-blue-600';
    if (temp <= -30) return 'text-blue-500';
    if (temp <= -10) return 'text-cyan-500';
    if (temp <= 0) return 'text-green-500';
    return 'text-orange-500';
};
</script>

<template>
    <Card variant="mars">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-2">
                        {{ statistics.month_name }}
                    </h2>
                    <p class="text-orange-100 text-sm">
                        {{ statistics.ls_range }} • {{ statistics.season_description }}
                    </p>
                </div>
                <Badge :variant="seasonVariant" class="mt-2 sm:mt-0">
                    {{ statistics.season }}
                </Badge>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="p-6 bg-white dark:bg-gray-900">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Average Temperature -->
                <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-950/30 dark:to-red-950/30 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <ThermometerSun class="w-8 h-8 text-orange-600" />
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Средняя температура</p>
                    <p :class="`text-4xl font-bold ${getTemperatureColor(statistics.statistics.average_temp)}`">
                        {{ statistics.statistics.average_temp }}°C
                    </p>
                </div>

                <!-- Min Temperature -->
                <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-950/30 dark:to-cyan-950/30 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <TrendingDown class="w-8 h-8 text-blue-600" />
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Минимум за все время</p>
                    <p class="text-4xl font-bold text-blue-600">
                        {{ statistics.statistics.absolute_min }}°C
                    </p>
                    <div class="flex items-center justify-center mt-1">
                        <ThermometerSnowflake class="w-4 h-4 text-blue-500 mr-1" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">Самая холодная точка</span>
                    </div>
                </div>

                <!-- Max Temperature -->
                <div class="text-center p-4 bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-950/30 dark:to-orange-950/30 rounded-lg">
                    <div class="flex items-center justify-center mb-2">
                        <TrendingUp class="w-8 h-8 text-red-600" />
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Максимум за все время</p>
                    <p class="text-4xl font-bold text-red-600">
                        {{ statistics.statistics.absolute_max }}°C
                    </p>
                    <div class="flex items-center justify-center mt-1">
                        <ThermometerSun class="w-4 h-4 text-red-500 mr-1" />
                        <span class="text-xs text-gray-500 dark:text-gray-400">Самая теплая точка</span>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <Database class="w-5 h-5 mr-3 text-orange-600" />
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Всего наблюдений</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            {{ statistics.statistics.total_sols }} солов
                        </p>
                    </div>
                </div>
                <div class="flex items-center">
                    <Calendar class="w-5 h-5 mr-3 text-orange-600" />
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Период (земные даты)</p>
                        <p class="text-xs font-medium text-gray-900 dark:text-gray-100">
                            {{ statistics.date_range.first_date }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            до {{ statistics.date_range.last_date }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Temperature Interpretation -->
            <div class="mt-6 p-4 bg-sky-50 dark:bg-sky-950/30 rounded-lg border border-sky-200 dark:border-sky-900">
                <p class="text-sm text-sky-800 dark:text-sky-300">
                    <strong>Интерпретация:</strong> В течение этого марсианского месяца температуры значительно варьировались
                    в диапазоне {{ statistics.statistics.absolute_max - statistics.statistics.absolute_min }}°C.
                    Средняя температура {{ statistics.statistics.average_temp }}°C типична для данного сезона ({{ statistics.season.toLowerCase() }}) в северном полушарии Марса.
                </p>
            </div>
        </div>
    </Card>
</template>
