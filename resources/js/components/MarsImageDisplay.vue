<script setup lang="ts">
import { ref } from 'vue';
import Card from './ui/Card.vue';
import Badge from './ui/Badge.vue';
import MarkdownRenderer from './MarkdownRenderer.vue';
import { ImageIcon, Sparkles } from 'lucide-vue-next';
import type { GeneratedImage } from '@/types/mars';

interface Props {
    generatedImage: GeneratedImage;
}

const props = defineProps<Props>();
const imageLoaded = ref(false);

const onImageLoad = () => {
    imageLoaded.value = true;
};
</script>

<template>
    <Card variant="mars">
        <!-- Header with gradient -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <ImageIcon class="w-8 h-8" />
                    <div>
                        <h2 class="text-2xl font-bold">
                            Изображение Марса
                        </h2>
                        <p class="text-purple-100 text-sm">
                            Месяц {{ generatedImage.month }} • {{ generatedImage.weather_context.season }}
                        </p>
                    </div>
                </div>
                <Badge variant="default">
                    <Sparkles class="w-4 h-4 mr-1" />
                    AI Generated
                </Badge>
            </div>
        </div>

        <!-- Image Container -->
        <div class="relative bg-gray-100 dark:bg-gray-800">
            <!-- Loading Skeleton -->
            <div
                v-if="!imageLoaded"
                class="absolute inset-0 flex items-center justify-center"
            >
                <div class="animate-pulse flex flex-col items-center gap-4">
                    <ImageIcon class="w-16 h-16 text-gray-400" />
                    <p class="text-gray-500">Загрузка изображения...</p>
                </div>
            </div>

            <!-- Generated Image -->
            <img
                :src="generatedImage.image"
                alt="Сгенерированное изображение Марса"
                class="w-full h-auto transition-opacity duration-500"
                :class="{ 'opacity-0': !imageLoaded, 'opacity-100': imageLoaded }"
                @load="onImageLoad"
                loading="lazy"
            />
        </div>

        <!-- Interpretation Section -->
        <div class="p-6 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center gap-2">
                    <Sparkles class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    Интерпретация AI
                </h3>
                <MarkdownRenderer :content="generatedImage.interpretation" />
            </div>

            <!-- Weather Context -->
            <div class="mt-6 p-4 bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-950/30 dark:to-red-950/30 rounded-lg border border-orange-200 dark:border-orange-900">
                <h4 class="text-sm font-semibold text-orange-900 dark:text-orange-300 mb-2">
                    Погодные условия:
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-orange-800 dark:text-orange-400">
                    <div>
                        <strong>Сезон:</strong> {{ generatedImage.weather_context.season_description }}
                    </div>
                    <div>
                        <strong>Температура:</strong> {{ generatedImage.weather_context.statistics.average_temp }}°C (средняя)
                    </div>
                    <div>
                        <strong>Диапазон:</strong> {{ generatedImage.weather_context.statistics.absolute_min }}°C до {{ generatedImage.weather_context.statistics.absolute_max }}°C
                    </div>
                    <div>
                        <strong>Время:</strong> Восход Солнца
                    </div>
                </div>
            </div>
        </div>
    </Card>
</template>
