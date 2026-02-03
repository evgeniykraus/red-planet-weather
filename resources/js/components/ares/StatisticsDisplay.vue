<script setup lang="ts">
import { computed } from 'vue';
import { ThermometerSun, ThermometerSnowflake, TrendingDown, TrendingUp, Calendar, Database, Sprout, Sun, Leaf, Snowflake, Globe } from 'lucide-vue-next';

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

const getTemperatureColor = (temp: number): string => {
    if (temp <= -50) return 'text-blue-400';
    if (temp <= -30) return 'text-cyan-400';
    if (temp <= -10) return 'text-green-400';
    if (temp <= 0) return 'text-yellow-400';
    return 'text-orange-400';
};

const seasonIcon = computed(() => {
    const season = props.statistics.season.toLowerCase();
    if (season.includes('весна') || season.includes('spring')) return Sprout;
    if (season.includes('лето') || season.includes('summer')) return Sun;
    if (season.includes('осень') || season.includes('autumn')) return Leaf;
    if (season.includes('зима') || season.includes('winter')) return Snowflake;
    return Globe;
});
</script>

<template>
    <div class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-primary-orange/20 border-b border-primary-orange/30 p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h2 class="text-white text-2xl font-bold uppercase tracking-wider font-orbitron mb-2">
                        {{ statistics.month_name }}
                    </h2>
                    <div class="flex items-center gap-4 text-sm text-text-muted">
                        <span class="uppercase tracking-wide">{{ statistics.ls_range }}</span>
                        <span class="text-primary-orange">•</span>
                        <span>{{ statistics.season_description }}</span>
                    </div>
                </div>
                <div class="px-4 py-2 bg-primary-orange/30 border border-primary-orange rounded-lg flex items-center gap-2">
                    <component :is="seasonIcon" class="w-4 h-4 text-white" />
                    <span class="text-white font-bold uppercase tracking-wide text-sm">
                        {{ statistics.season }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <!-- Average Temperature -->
                <div class="bg-deep-background/50 border border-primary-orange/20 rounded-lg p-4">
                    <div class="flex items-center justify-center mb-2">
                        <ThermometerSun class="w-6 h-6 text-primary-orange" />
                    </div>
                    <p class="text-xs text-text-muted text-center uppercase tracking-wide mb-2">Средняя температура</p>
                    <p :class="`text-3xl font-bold text-center font-mono ${getTemperatureColor(statistics.statistics.average_temp)}`">
                        {{ statistics.statistics.average_temp }}°C
                    </p>
                </div>

                <!-- Min Temperature -->
                <div class="bg-deep-background/50 border border-primary-orange/20 rounded-lg p-4">
                    <div class="flex items-center justify-center mb-2">
                        <TrendingDown class="w-6 h-6 text-blue-400" />
                    </div>
                    <p class="text-xs text-text-muted text-center uppercase tracking-wide mb-2">Абсолютный минимум</p>
                    <p class="text-3xl font-bold text-center font-mono text-blue-400">
                        {{ statistics.statistics.absolute_min }}°C
                    </p>
                    <div class="flex items-center justify-center mt-2 gap-1">
                        <ThermometerSnowflake class="w-4 h-4 text-blue-400" />
                        <span class="text-xs text-text-muted">Самая холодная точка</span>
                    </div>
                </div>

                <!-- Max Temperature -->
                <div class="bg-deep-background/50 border border-primary-orange/20 rounded-lg p-4">
                    <div class="flex items-center justify-center mb-2">
                        <TrendingUp class="w-6 h-6 text-red-400" />
                    </div>
                    <p class="text-xs text-text-muted text-center uppercase tracking-wide mb-2">Абсолютный максимум</p>
                    <p class="text-3xl font-bold text-center font-mono text-red-400">
                        {{ statistics.statistics.absolute_max }}°C
                    </p>
                    <div class="flex items-center justify-center mt-2 gap-1">
                        <ThermometerSun class="w-4 h-4 text-red-400" />
                        <span class="text-xs text-text-muted">Самая теплая точка</span>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-primary-orange/20">
                <div class="flex items-start gap-3">
                    <Database class="w-5 h-5 text-primary-orange flex-shrink-0 mt-1" />
                    <div>
                        <p class="text-xs text-text-muted uppercase tracking-wide">Всего наблюдений</p>
                        <p class="text-lg font-bold text-white font-mono">
                            {{ statistics.statistics.total_sols }} солов
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <Calendar class="w-5 h-5 text-primary-orange flex-shrink-0 mt-1" />
                    <div>
                        <p class="text-xs text-text-muted uppercase tracking-wide">Период (земные даты)</p>
                        <p class="text-sm font-medium text-white">
                            {{ statistics.date_range.first_date }}
                        </p>
                        <p class="text-xs text-text-muted">
                            до {{ statistics.date_range.last_date }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Temperature Interpretation -->
            <div class="mt-6 p-4 bg-primary-orange/10 rounded-lg border border-primary-orange/30">
                <p class="text-sm text-text-muted leading-relaxed">
                    <span class="text-primary-orange font-bold uppercase">Интерпретация:</span>
                    В течение этого марсианского месяца температуры варьировались
                    от <span class="text-white font-bold">{{ statistics.statistics.absolute_min }}°C</span>
                    до <span class="text-white font-bold">{{ statistics.statistics.absolute_max }}°C</span>.
                    Средняя температура <span class="text-white font-bold">{{ statistics.statistics.average_temp }}°C</span> типична для данного сезона ({{ statistics.season.toLowerCase() }}) в северном полушарии Марса.
                </p>
            </div>
        </div>
    </div>
</template>
