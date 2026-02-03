<script setup lang="ts">
import { Info, Clock, Calendar, Thermometer, Sprout, Sun, Leaf, Snowflake } from 'lucide-vue-next';

const monthsInfo = [
    { month: 1, ls: '0°-30°', season: 'Весна', icon: Sprout },
    { month: 2, ls: '30°-60°', season: 'Весна', icon: Sprout },
    { month: 3, ls: '60°-90°', season: 'Весна', icon: Sprout },
    { month: 4, ls: '90°-120°', season: 'Лето', icon: Sun },
    { month: 5, ls: '120°-150°', season: 'Лето', icon: Sun },
    { month: 6, ls: '150°-180°', season: 'Лето', icon: Sun },
    { month: 7, ls: '180°-210°', season: 'Осень', icon: Leaf },
    { month: 8, ls: '210°-240°', season: 'Осень', icon: Leaf },
    { month: 9, ls: '240°-270°', season: 'Осень', icon: Leaf },
    { month: 10, ls: '270°-300°', season: 'Зима', icon: Snowflake },
    { month: 11, ls: '300°-330°', season: 'Зима', icon: Snowflake },
    { month: 12, ls: '330°-360°', season: 'Зима', icon: Snowflake },
];
</script>

<template>
    <div class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg p-6">
        <div class="flex items-center gap-3 mb-6">
            <Info class="w-6 h-6 text-primary-orange" />
            <h2 class="text-white text-xl font-bold uppercase tracking-wider font-orbitron">
                О данных миссии
            </h2>
        </div>

        <div class="space-y-4 mb-6">
            <div class="flex items-start gap-3">
                <Clock class="w-5 h-5 text-primary-orange flex-shrink-0 mt-0.5" />
                <div>
                    <p class="font-semibold text-white text-sm uppercase tracking-wide">Что такое сол?</p>
                    <p class="text-sm text-text-muted mt-1">
                        Марсианский сол длится 24 часа, 39 минут и 35 секунд — примерно на 40 минут дольше земных суток.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <Calendar class="w-5 h-5 text-primary-orange flex-shrink-0 mt-0.5" />
                <div>
                    <p class="font-semibold text-white text-sm uppercase tracking-wide">Марсианский год</p>
                    <p class="text-sm text-text-muted mt-1">
                        Марсианский год ≈ 668.59 солов ≈ 687 земных дней (~1.88 земного года).
                        Марсианские месяцы делятся по солнечной долготе (Ls), каждый охватывает 30°.
                    </p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <Thermometer class="w-5 h-5 text-primary-orange flex-shrink-0 mt-0.5" />
                <div>
                    <p class="font-semibold text-white text-sm uppercase tracking-wide">Данные о температуре</p>
                    <p class="text-sm text-text-muted mt-1">
                        Все температуры указаны в градусах Цельсия (°C). Данные собраны марсоходом NASA Curiosity
                        в кратере Гейла с Sol 0 (6 августа 2012 года).
                    </p>
                </div>
            </div>
        </div>

        <!-- Months Reference Table -->
        <div class="mt-6 pt-6 border-t border-primary-orange/30">
            <h3 class="text-white text-sm font-bold uppercase tracking-wide mb-3">
                Справочник месяцев
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
                <div
                    v-for="info in monthsInfo"
                    :key="info.month"
                    class="bg-deep-background/50 border border-primary-orange/20 rounded p-2"
                >
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-white font-mono">
                            М{{ info.month }}
                        </span>
                        <component :is="info.icon" class="w-4 h-4 text-primary-orange" />
                    </div>
                    <div class="text-xs text-text-muted">
                        Ls {{ info.ls }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Source -->
        <div class="mt-6 pt-6 border-t border-primary-orange/30">
            <p class="text-xs text-text-muted">
                <span class="text-primary-orange font-bold uppercase">Источник:</span> Марсоход NASA Curiosity через
                <a
                    href="https://api.maas2.apollorion.com"
                    target="_blank"
                    class="text-primary-orange hover:text-white underline transition-colors"
                >
                    Apollorion MAAS API
                </a>
            </p>
        </div>
    </div>
</template>
