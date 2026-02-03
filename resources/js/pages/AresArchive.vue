<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { useMarsAtmosphere, type AtmosphereConfig } from '@/composables/useMarsAtmosphere';
import { atmosphereStore, updateAtmosphere, randomizeAtmosphere } from '@/store/atmosphere';
import ControlPanel from '@/components/ares/ControlPanel.vue';
import MonthSelector from '@/components/ares/MonthSelector.vue';
import StatisticsDisplay from '@/components/ares/StatisticsDisplay.vue';
import StatisticsLoadingProgress from '@/components/ares/StatisticsLoadingProgress.vue';
import ImageGenerator from '@/components/ares/ImageGenerator.vue';
import InfoPanel from '@/components/ares/InfoPanel.vue';
import { ImageIcon } from 'lucide-vue-next';
import type { Statistics, GeneratedImage } from '@/types/mars';
import GenerateMarsImage from '@/actions/App/Http/Controllers/GenerateMarsImageController';
import mars from '@/routes/mars';

interface Props {
    availableMonths: number[];
    statistics?: Statistics | null;
    selectedMonth?: number;
    generatedImage?: GeneratedImage | null;
}

const props = defineProps<Props>();

const canvasRef = ref<HTMLCanvasElement | null>(null);
const isLoadingStatistics = ref(false);

const form = useForm({
    mars_month: props.selectedMonth || 1,
});

useMarsAtmosphere(canvasRef);

const updateConfig = (newConfig: AtmosphereConfig) => {
    updateAtmosphere(newConfig);
};

const handleRandomize = () => {
    randomizeAtmosphere();
};

const generateImage = () => {
    form.post(GenerateMarsImage.url(), {
        preserveScroll: true,
        preserveState: true,
    });
};

const handleLoadingChange = (loading: boolean) => {
    isLoadingStatistics.value = loading;
};

// Update form when selectedMonth changes
watch(() => props.selectedMonth, (newMonth) => {
    if (newMonth) {
        form.mars_month = newMonth;
    }
});

onMounted(() => {
    // Canvas logic is handled by useMarsAtmosphere
});
</script>

<template>
    <Head title="ARES ARCHIVE - Исторический архив погодных данных">
        <meta name="description" content="Интерактивная визуализация исторических погодных данных Марса">
    </Head>

    <div class="relative min-h-screen overflow-hidden bg-deep-background">
        <!-- Canvas Background -->
        <canvas
            ref="canvasRef"
            class="fixed inset-0 w-full h-full"
            style="z-index: 1"
        ></canvas>

        <!-- Main Content -->
        <div class="relative z-10">
            <!-- Header -->
            <header class="border-b border-primary-orange/30 bg-deep-background/80 backdrop-blur-md">
                <div class="max-w-7xl mx-auto px-6 py-6">
                    <div class="flex items-center justify-between">
                        <!-- Logo -->
                        <div>
                            <h1 class="text-primary-orange text-4xl font-bold uppercase tracking-wider font-orbitron mb-1">
                                ARES <span class="text-white">ARCHIVE</span>
                            </h1>
                            <p class="text-text-muted text-sm uppercase tracking-widest font-rajdhani">
                                Исторический архив погодных данных
                            </p>
                        </div>

                        <!-- Navigation & Current Sol Status -->
                        <div class="flex items-center gap-6">
                            <!-- Gallery Button -->
                            <Link 
                                :href="mars.gallery.url()"
                                class="flex items-center gap-2 px-4 py-2 bg-primary-orange/10 hover:bg-primary-orange/20 border border-primary-orange/30 hover:border-primary-orange rounded-lg transition-all text-primary-orange hover:text-white"
                            >
                                <ImageIcon :size="18" />
                                <span class="text-sm uppercase tracking-wide font-rajdhani font-semibold">Галерея</span>
                            </Link>

                            <!-- Current Sol Status -->
                            <div class="text-right">
                                <div class="text-text-muted text-sm uppercase tracking-wide mb-1">
                                    Текущий СОЛ
                                </div>
                                <div class="text-primary-orange text-4xl font-bold font-mono">
                                    4792
                                </div>
                                <div class="text-text-muted text-xs uppercase tracking-wider">
                                    GALE CRATER
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
                <!-- Month Selector -->
                <div class="animate-fade-in">
                    <MonthSelector
                        :available-months="availableMonths"
                        :selected-month="selectedMonth"
                        @loading-change="handleLoadingChange"
                    />
                </div>

                <!-- Statistics Loading -->
                <transition
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 transform scale-95"
                    enter-to-class="opacity-100 transform scale-100"
                    leave-active-class="transition duration-300 ease-in"
                    leave-from-class="opacity-100 transform scale-100"
                    leave-to-class="opacity-0 transform scale-95"
                >
                    <div v-if="isLoadingStatistics" class="animate-fade-in">
                        <StatisticsLoadingProgress />
                    </div>
                </transition>

                <!-- Statistics Display -->
                <transition
                    enter-active-class="transition duration-500 ease-out"
                    enter-from-class="opacity-0 transform scale-95"
                    enter-to-class="opacity-100 transform scale-100"
                    leave-active-class="transition duration-300 ease-in"
                    leave-from-class="opacity-100 transform scale-100"
                    leave-to-class="opacity-0 transform scale-95"
                >
                    <div v-if="statistics && !isLoadingStatistics" class="animate-fade-in">
                        <StatisticsDisplay :statistics="statistics" />
                    </div>
                </transition>

                <!-- Image Generator -->
                <transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="opacity-0 transform scale-95"
                    enter-to-class="opacity-100 transform scale-100"
                >
                    <div v-if="selectedMonth" class="animate-fade-in">
                        <ImageGenerator
                            :selected-month="selectedMonth"
                            :is-generating="form.processing"
                            :generated-image="generatedImage"
                            @generate="generateImage"
                        />
                    </div>
                </transition>

                <!-- Info Panel -->
                <div class="animate-fade-in">
                    <InfoPanel />
                </div>
            </div>
        </div>

        <!-- Control Panel (Sidebar) -->
        <ControlPanel
            :config="atmosphereStore"
            @update="updateConfig"
            @randomize="handleRandomize"
        />
    </div>
</template>

<style>
/* Ensure the canvas is behind content but visible */
canvas {
    pointer-events: none;
}
</style>
