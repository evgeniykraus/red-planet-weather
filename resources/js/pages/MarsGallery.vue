<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import ImageLightbox from '@/components/ImageLightbox.vue';
import { useMarsAtmosphere, type AtmosphereConfig } from '@/composables/useMarsAtmosphere';
import { atmosphereStore, updateAtmosphere, randomizeAtmosphere } from '@/store/atmosphere';
import ControlPanel from '@/components/ares/ControlPanel.vue';
import { ImageIcon, Filter, Home } from 'lucide-vue-next';

interface MarsImage {
    id: number;
    mars_month: number;
    image_url: string;
    interpretation: string;
    weather_context: any;
    created_at: string;
}

interface Props {
    images: MarsImage[];
    availableMonths: number[];
    monthsWithImages: number[];
}

const props = defineProps<Props>();

const selectedMonth = ref<number | null>(null);
const canvasRef = ref<HTMLCanvasElement | null>(null);

// Состояние lightbox
const isLightboxOpen = ref(false);
const currentImageIndex = ref(0);

useMarsAtmosphere(canvasRef);

const updateConfig = (newConfig: AtmosphereConfig) => {
    updateAtmosphere(newConfig);
};

const handleRandomize = () => {
    randomizeAtmosphere();
};

const filteredImages = computed(() => {
    const filtered = selectedMonth.value 
        ? props.images.filter(img => img.mars_month === selectedMonth.value)
        : props.images;
    
    // Сортировка: сначала по месяцу (по возрастанию), затем по дате (от новых к старым)
    return [...filtered].sort((a, b) => {
        // Сначала сравниваем месяцы
        if (a.mars_month !== b.mars_month) {
            return a.mars_month - b.mars_month;
        }
        // Если месяцы одинаковые, сортируем по дате (от новых к старым)
        return new Date(b.created_at).getTime() - new Date(a.created_at).getTime();
    });
});

const imagesByMonth = computed(() => {
    const grouped: Record<number, MarsImage[]> = {};
    filteredImages.value.forEach(image => {
        if (!grouped[image.mars_month]) {
            grouped[image.mars_month] = [];
        }
        grouped[image.mars_month].push(image);
    });
    return grouped;
});

// Открытие lightbox
const openLightbox = (image: MarsImage) => {
    const index = filteredImages.value.findIndex(img => img.id === image.id);
    if (index !== -1) {
        currentImageIndex.value = index;
        isLightboxOpen.value = true;
    }
};

// Закрытие lightbox
const closeLightbox = () => {
    isLightboxOpen.value = false;
};

onMounted(() => {
    // Canvas logic is handled by useMarsAtmosphere
});
</script>

<template>
    <Head title="ARES ARCHIVE - Галерея изображений" />
    
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
                                ARES <span class="text-white">GALLERY</span>
                            </h1>
                            <p class="text-text-muted text-sm uppercase tracking-widest font-rajdhani">
                                Архив сгенерированных изображений
                            </p>
                        </div>

                        <!-- Navigation & Stats -->
                        <div class="flex items-center gap-6">
                            <!-- Home Button -->
                            <Link 
                                href="/"
                                class="flex items-center gap-2 px-4 py-2 bg-primary-orange/10 hover:bg-primary-orange/20 border border-primary-orange/30 hover:border-primary-orange rounded-lg transition-all text-primary-orange hover:text-white"
                            >
                                <Home :size="18" />
                                <span class="text-sm uppercase tracking-wide font-rajdhani font-semibold">Главная</span>
                            </Link>

                            <!-- Stats -->
                            <div class="text-right">
                                <div class="text-text-muted text-sm uppercase tracking-wide mb-1">
                                    Всего изображений
                                </div>
                                <div class="text-primary-orange text-4xl font-bold font-mono">
                                    {{ images.length }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <div class="max-w-7xl mx-auto px-6 py-8 space-y-8">
                <!-- Filter -->
                <div class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <Filter class="w-6 h-6 text-primary-orange" />
                        <div class="flex-1">
                            <label class="block text-sm font-medium mb-2 text-white uppercase tracking-wide">
                                Фильтр по месяцу
                            </label>
                            <select 
                                v-model="selectedMonth"
                                class="w-full bg-deep-background border border-primary-orange/30 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-primary-orange transition-colors cursor-pointer"
                            >
                                <option :value="null">Все месяцы</option>
                                <option 
                                    v-for="m in monthsWithImages" 
                                    :key="m" 
                                    :value="m"
                                >
                                    Месяц {{ m }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Gallery by Month -->
                <div v-for="(monthImages, month) in imagesByMonth" :key="month" class="space-y-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-white text-2xl font-bold uppercase tracking-wider font-orbitron">
                            Месяц {{ month }}
                        </h2>
                        <div class="px-3 py-1 bg-primary-orange/30 border border-primary-orange rounded-lg">
                            <span class="text-white text-sm font-bold">
                                {{ monthImages.length }}
                            </span>
                        </div>
                        <div class="flex-1 h-0.5 bg-primary-orange/30"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div 
                            v-for="image in monthImages" 
                            :key="image.id"
                            class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg overflow-hidden hover:border-primary-orange transition-all hover:shadow-2xl hover:shadow-primary-orange/20 group cursor-pointer"
                            @click="openLightbox(image)"
                        >
                            <!-- Image -->
                            <div class="relative aspect-video bg-deep-background/50">
                                <img 
                                    :src="image.image_url" 
                                    :alt="`Mars Month ${image.mars_month}`"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                />
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center">
                                    <ImageIcon class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" />
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="px-3 py-1 bg-primary-orange/20 border border-primary-orange/30 rounded">
                                        <span class="text-primary-orange text-xs font-bold uppercase">
                                            Месяц {{ image.mars_month }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-text-muted font-mono">
                                        {{ new Date(image.created_at).toLocaleDateString('ru') }}
                                    </span>
                                </div>

                                <!-- Interpretation -->
                                <details class="mt-2">
                                    <summary class="cursor-pointer text-sm font-medium text-white hover:text-primary-orange uppercase tracking-wide list-none flex items-center gap-2">
                                        <span class="text-primary-orange">▸</span> Интерпретация ИИ
                                    </summary>
                                    <div class="mt-3 pl-4 border-l border-primary-orange/20">
                                        <MarkdownRenderer :content="image.interpretation" />
                                    </div>
                                </details>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredImages.length === 0" class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg p-12 text-center">
                    <ImageIcon class="w-16 h-16 mx-auto text-primary-orange/50 mb-4" />
                    <p class="text-text-muted text-lg">
                        Нет изображений для отображения
                    </p>
                </div>
            </div>
        </div>

        <!-- Control Panel -->
        <ControlPanel
            :config="atmosphereStore"
            @update="updateConfig"
            @randomize="handleRandomize"
        />

        <!-- Lightbox -->
        <ImageLightbox
            :images="filteredImages"
            :initial-index="currentImageIndex"
            :is-open="isLightboxOpen"
            @close="closeLightbox"
        />
    </div>
</template>

<style>
/* Ensure the canvas is behind content but visible */
canvas {
    pointer-events: none;
}
</style>
