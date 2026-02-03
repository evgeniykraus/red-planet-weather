<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { X, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import Badge from './ui/Badge.vue';
import MarkdownRenderer from './MarkdownRenderer.vue';

interface Image {
    id: number;
    mars_month: number;
    image_url: string;
    interpretation: string;
    created_at: string;
}

interface Props {
    images: Image[];
    initialIndex: number;
    isOpen: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    close: [];
}>();

const currentIndex = ref(props.initialIndex);

// Текущее изображение
const currentImage = computed(() => props.images[currentIndex.value]);

// Навигация с бесконечной прокруткой
const canGoPrev = computed(() => props.images.length > 1);
const canGoNext = computed(() => props.images.length > 1);

const goToPrev = () => {
    if (props.images.length > 1) {
        if (currentIndex.value === 0) {
            // Если в начале, переходим в конец
            currentIndex.value = props.images.length - 1;
        } else {
            currentIndex.value--;
        }
    }
};

const goToNext = () => {
    if (props.images.length > 1) {
        if (currentIndex.value === props.images.length - 1) {
            // Если в конце, переходим в начало
            currentIndex.value = 0;
        } else {
            currentIndex.value++;
        }
    }
};

const close = () => {
    emit('close');
};

// Обработка нажатий клавиш
const handleKeydown = (e: KeyboardEvent) => {
    if (!props.isOpen) return;
    
    if (e.key === 'Escape') {
        close();
    } else if (e.key === 'ArrowLeft') {
        goToPrev();
    } else if (e.key === 'ArrowRight') {
        goToNext();
    }
};

// Блокировка скролла при открытом лайтбоксе
watch(() => props.isOpen, (isOpen) => {
    if (isOpen) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

// Сброс индекса при изменении initialIndex
watch(() => props.initialIndex, (newIndex) => {
    currentIndex.value = newIndex;
});

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition name="lightbox">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/95 backdrop-blur-sm"
                @click.self="close"
            >
                <!-- Кнопка закрытия -->
                <button
                    @click="close"
                    class="absolute top-4 right-4 p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer z-10"
                    aria-label="Закрыть"
                >
                    <X class="w-6 h-6" />
                </button>

                <!-- Счетчик изображений -->
                <div class="absolute top-4 left-4 z-10">
                    <Badge variant="default">
                        {{ currentIndex + 1 }} / {{ images.length }}
                    </Badge>
                </div>

                <!-- Контейнер слайдера -->
                <div class="w-full h-full flex items-center justify-center p-4 md:p-8">
                    <!-- Кнопка "Назад" -->
                    <button
                        v-if="canGoPrev"
                        @click="goToPrev"
                        class="absolute left-4 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer z-10"
                        aria-label="Предыдущее изображение"
                    >
                        <ChevronLeft class="w-8 h-8" />
                    </button>

                    <!-- Основной контент -->
                    <div class="max-w-7xl w-full h-full flex flex-col lg:flex-row gap-6 items-stretch">
                        <!-- Изображение -->
                        <div class="flex-1 flex items-center justify-center min-h-0 overflow-hidden">
                            <Transition name="slide-fade" mode="out-in">
                                <img
                                    :key="currentImage.id"
                                    :src="currentImage.image_url"
                                    :alt="`Mars Month ${currentImage.mars_month}`"
                                    class="max-w-full max-h-[80vh] lg:max-h-full object-contain rounded-lg shadow-2xl"
                                />
                            </Transition>
                        </div>

                        <!-- Информация -->
                        <div class="lg:w-96 bg-white dark:bg-gray-900 rounded-lg p-6 overflow-y-auto lg:max-h-[85vh] flex-shrink-0 border border-gray-200 dark:border-gray-700">
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <Badge>Месяц {{ currentImage.mars_month }}</Badge>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ new Date(currentImage.created_at).toLocaleDateString('ru', { 
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                        }) }}
                                    </span>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                    <span class="text-2xl">🤖</span>
                                    Интерпретация AI
                                </h3>
                                <div class="prose-sm">
                                    <MarkdownRenderer :content="currentImage.interpretation" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопка "Вперед" -->
                    <button
                        v-if="canGoNext"
                        @click="goToNext"
                        class="absolute right-4 p-3 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors cursor-pointer z-10"
                        aria-label="Следующее изображение"
                    >
                        <ChevronRight class="w-8 h-8" />
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
/* Анимация появления/исчезновения лайтбокса */
.lightbox-enter-active,
.lightbox-leave-active {
    transition: opacity 0.3s ease;
}

.lightbox-enter-from,
.lightbox-leave-to {
    opacity: 0;
}

/* Анимация смены изображений */
.slide-fade-enter-active {
    transition: all 0.3s ease;
}

.slide-fade-leave-active {
    transition: all 0.2s ease;
}

.slide-fade-enter-from {
    opacity: 0;
    transform: translateX(30px);
}

.slide-fade-leave-to {
    opacity: 0;
    transform: translateX(-30px);
}
</style>
