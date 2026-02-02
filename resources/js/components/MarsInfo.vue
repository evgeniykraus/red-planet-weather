<script setup lang="ts">
import Card from './ui/Card.vue';
import Badge from './ui/Badge.vue';
import { Info, Clock, Calendar, Thermometer } from 'lucide-vue-next';

const monthsInfo = [
    { month: 1, ls: '0°-30°', season: 'Ранняя весна', variant: 'spring' },
    { month: 2, ls: '30°-60°', season: 'Середина весны', variant: 'spring' },
    { month: 3, ls: '60°-90°', season: 'Поздняя весна', variant: 'spring' },
    { month: 4, ls: '90°-120°', season: 'Раннее лето', variant: 'summer' },
    { month: 5, ls: '120°-150°', season: 'Середина лета', variant: 'summer' },
    { month: 6, ls: '150°-180°', season: 'Позднее лето', variant: 'summer' },
    { month: 7, ls: '180°-210°', season: 'Ранняя осень', variant: 'autumn' },
    { month: 8, ls: '210°-240°', season: 'Середина осени', variant: 'autumn' },
    { month: 9, ls: '240°-270°', season: 'Поздняя осень', variant: 'autumn' },
    { month: 10, ls: '270°-300°', season: 'Ранняя зима', variant: 'winter' },
    { month: 11, ls: '300°-330°', season: 'Середина зимы', variant: 'winter' },
    { month: 12, ls: '330°-360°', season: 'Поздняя зима', variant: 'winter' },
];
</script>

<template>
    <Card>
        <div class="p-6 bg-gradient-to-br from-gray-50 to-orange-50 dark:from-gray-900 dark:to-orange-950/30">
            <div class="flex items-center mb-4">
                <Info class="w-6 h-6 mr-2 text-orange-600" />
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    О данных марсианской погоды
                </h2>
            </div>

            <div class="space-y-4 mb-6">
                <div class="flex items-start">
                    <Clock class="w-5 h-5 mr-3 mt-0.5 text-orange-600 flex-shrink-0" />
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">Что такое сол?</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Марсианский сол длится 24 часа, 39 минут и 35 секунд — примерно на 40 минут дольше земных суток.
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <Calendar class="w-5 h-5 mr-3 mt-0.5 text-orange-600 flex-shrink-0" />
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">Марсианский год</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Марсианский год ≈ 668.59 солов ≈ 687 земных дней (~1.88 земного года).
                            Марсианские месяцы делятся по солнечной долготе (Ls), каждый охватывает 30°.
                        </p>
                    </div>
                </div>

                <div class="flex items-start">
                    <Thermometer class="w-5 h-5 mr-3 mt-0.5 text-orange-600 flex-shrink-0" />
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">Данные о температуре</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Все температуры указаны в градусах Цельсия (°C). Данные собраны марсоходом NASA Curiosity
                            в кратере Гейла с Sol 0 (6 августа 2012 года).
                        </p>
                    </div>
                </div>
            </div>

            <!-- Months Reference Table -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">
                    Справочник марсианских месяцев
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                    <div
                        v-for="info in monthsInfo"
                        :key="info.month"
                        class="flex items-center justify-between p-2 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700"
                    >
                        <div class="flex items-center">
                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100 mr-2">
                                Месяц {{ info.month }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                (Ls {{ info.ls }})
                            </span>
                        </div>
                        <Badge :variant="info.variant as any">
                            {{ info.season }}
                        </Badge>
                    </div>
                </div>
            </div>

            <!-- Data Source -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    <strong>Источник данных:</strong> Марсоход NASA Curiosity через
                    <a
                        href="https://api.maas2.apollorion.com"
                        target="_blank"
                        class="text-orange-600 hover:text-orange-700 underline"
                    >
                        Apollorion MAAS API
                    </a>
                </p>
            </div>
        </div>
    </Card>
</template>
