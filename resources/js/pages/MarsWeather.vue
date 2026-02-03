<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import MonthSearch from '@/components/MonthSearch.vue';
import MonthStatistics from '@/components/MonthStatistics.vue';
import MarsInfo from '@/components/MarsInfo.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import { Globe } from 'lucide-vue-next';

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
    availableMonths: number[];
    statistics?: Statistics | null;
    selectedMonth?: number;
}

defineProps<Props>();
</script>

<template>
    <Head title="Данные о погоде на Марсе">
        <meta name="description" content="Исследуйте данные о марсианской погоде, собранные марсоходом NASA Curiosity">
    </Head>

    <div class="min-h-screen bg-gradient-to-b from-orange-50 via-red-50 to-orange-100 dark:from-gray-900 dark:via-red-950 dark:to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Theme Toggle (верхний правый угол) -->
            <div class="flex justify-end">
                <ThemeToggle />
            </div>

            <!-- Header -->
            <header class="text-center -mt-4">
                <div class="flex items-center justify-center mb-4">
                    <Globe class="w-12 h-12 sm:w-16 sm:h-16 text-orange-600 mr-3 animate-pulse" />
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600">
                        Данные о погоде на Марсе
                    </h1>
                </div>
                <p class="text-lg sm:text-xl text-gray-700 dark:text-gray-300 max-w-3xl mx-auto">
                    Исследуйте данные о марсианской погоде, собранные марсоходом NASA Curiosity с 6 августа 2012 года (Sol 0)
                </p>
            </header>

            <!-- Search Component -->
            <div class="animate-fade-in">
                <MonthSearch
                    :available-months="availableMonths"
                    :selected-month="selectedMonth"
                />
            </div>

            <!-- Statistics Display -->
            <transition
                enter-active-class="transition duration-500 ease-out"
                enter-from-class="opacity-0 transform scale-95"
                enter-to-class="opacity-100 transform scale-100"
                leave-active-class="transition duration-300 ease-in"
                leave-from-class="opacity-100 transform scale-100"
                leave-to-class="opacity-0 transform scale-95"
            >
                <div v-if="statistics" class="animate-fade-in">
                    <MonthStatistics :statistics="statistics" />
                </div>
            </transition>

            <!-- Info Component -->
            <div class="animate-fade-in">
                <MarsInfo />
            </div>

            <!-- Footer -->
            <footer class="mt-auto border-t border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 py-6">
                <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-gray-500 dark:text-gray-400">

                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900 dark:text-white tracking-wider">SE_TEAM</span>
                        <span>&copy; {{ new Date().getFullYear() }}</span>
                    </div>

                    <div class="text-center md:text-right">
                        <div class="mb-1">
                            Создано с ❤️ на <span class="text-gray-700 dark:text-gray-300">Laravel</span> + <span class="text-gray-700 dark:text-gray-300">Vue 3</span>
                        </div>
                    </div>

                </div>
            </footer>
        </div>
    </div>
</template>

<style scoped>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
