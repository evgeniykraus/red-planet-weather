<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/components/ui/Card.vue';
import Badge from '@/components/ui/Badge.vue';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import ImageLightbox from '@/components/ImageLightbox.vue';
import ThemeToggle from '@/components/ThemeToggle.vue';
import Select from '@/components/ui/Select.vue';
import Button from '@/components/ui/Button.vue';
import { ImageIcon, Filter, ArrowLeft } from 'lucide-vue-next';
import mars from '@/routes/mars';

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

// Состояние lightbox
const isLightboxOpen = ref(false);
const currentImageIndex = ref(0);

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
</script>

<template>
    <Head title="Галерея изображений Марса" />
    
    <div class="min-h-screen bg-gradient-to-b from-orange-50 via-red-50 to-orange-100 dark:from-gray-900 dark:via-red-950 dark:to-gray-900 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto space-y-8">
            <!-- Navigation (Back Button & Theme Toggle) -->
            <div class="flex justify-end gap-4 items-center">
                <Link :href="mars.index.url()">
                    <Button variant="outline" size="sm">
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Назад
                    </Button>
                </Link>
                <ThemeToggle />
            </div>

            <!-- Header -->
            <header class="text-center -mt-4">
                <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                    Галерея изображений Марса
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Всего изображений: {{ images.length }}
                </p>
            </header>
            
            <!-- Фильтр по месяцам -->
            <Card class="mb-8 p-6">
                <div class="flex items-center gap-4">
                    <Filter class="w-5 h-5 text-gray-600 dark:text-gray-400" />
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">
                            Фильтр по месяцу
                        </label>
                        <select 
                            v-model="selectedMonth"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-gray-900 dark:text-gray-100 cursor-pointer"
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
            </Card>
            
            <!-- Галерея по месяцам -->
            <div v-for="(monthImages, month) in imagesByMonth" :key="month" class="mb-12">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">
                    Месяц {{ month }}
                    <Badge variant="default" class="ml-2">
                        {{ monthImages.length }} изображений
                    </Badge>
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card 
                        v-for="image in monthImages" 
                        :key="image.id"
                        variant="mars"
                        class="overflow-hidden cursor-pointer hover:shadow-xl transition-shadow"
                    >
                        <!-- Изображение -->
                        <div 
                            class="relative aspect-video bg-gray-200 dark:bg-gray-800"
                            @click="openLightbox(image)"
                        >
                            <img 
                                :src="image.image_url" 
                                :alt="`Mars Month ${image.mars_month}`"
                                class="w-full h-full object-cover"
                                loading="lazy"
                            />
                            <div class="absolute inset-0 bg-black/0 hover:bg-black/10 transition-colors flex items-center justify-center">
                                <ImageIcon class="w-12 h-12 text-white opacity-0 hover:opacity-100 transition-opacity" />
                            </div>
                        </div>
                        
                        <!-- Информация -->
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <Badge>Месяц {{ image.mars_month }}</Badge>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ new Date(image.created_at).toLocaleDateString('ru') }}
                                </span>
                            </div>
                            
                            <!-- Интерпретация (свернутая) -->
                            <details class="mt-2">
                                <summary class="cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400">
                                    Показать интерпретацию
                                </summary>
                                <div class="mt-2 text-sm">
                                    <MarkdownRenderer :content="image.interpretation" />
                                </div>
                            </details>
                        </div>
                    </Card>
                </div>
            </div>
            
            <!-- Пустое состояние -->
            <div v-if="filteredImages.length === 0" class="text-center py-12">
                <ImageIcon class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                <p class="text-gray-600 dark:text-gray-400">
                    Нет изображений для отображения
                </p>
            </div>
        </div>
        
        <!-- Lightbox для просмотра изображений -->
        <ImageLightbox
            :images="filteredImages"
            :initial-index="currentImageIndex"
            :is-open="isLightboxOpen"
            @close="closeLightbox"
        />
    </div>
</template>
