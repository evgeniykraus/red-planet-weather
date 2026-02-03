<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { Sparkles, ImageIcon } from 'lucide-vue-next';
import type { GeneratedImage } from '@/types/mars';
import MarkdownRenderer from '@/components/MarkdownRenderer.vue';
import GenerationProgress from '@/components/ares/GenerationProgress.vue';

interface Props {
    selectedMonth?: number;
    isGenerating: boolean;
    generatedImage?: GeneratedImage | null;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    generate: [];
}>();

const generationComplete = ref(false);
const showResult = ref(false);
const isAnimating = ref(false);

const handleGenerate = () => {
    generationComplete.value = false;
    showResult.value = false;
    isAnimating.value = true;
    emit('generate');
};

const handleProgressFinished = () => {
    showResult.value = true;
    isAnimating.value = false;
};

// При монтировании, если изображение уже есть, показываем его сразу
onMounted(() => {
    if (props.generatedImage) {
        showResult.value = true;
        isAnimating.value = false;
    }
});

// Следим за появлением изображения
watch(() => props.generatedImage, (newValue, oldValue) => {
    if (newValue && !oldValue) {
        // Изображение только что появилось
        generationComplete.value = true;
    }
});

// Следим за сбросом состояния
watch(() => props.isGenerating, (newValue) => {
    if (!newValue && !props.generatedImage) {
        generationComplete.value = false;
        showResult.value = false;
        isAnimating.value = false;
    }
});
</script>

<template>
    <!-- Generation in Progress -->
    <GenerationProgress 
        v-if="selectedMonth && isAnimating" 
        :is-complete="generationComplete"
        @finished="handleProgressFinished"
    />

    <!-- Generate Button -->
    <div v-if="selectedMonth && !generatedImage && !isGenerating && !isAnimating" class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg p-6">
        <div class="flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-primary-orange/20 rounded-full flex items-center justify-center mb-4">
                <Sparkles class="w-8 h-8 text-primary-orange" />
            </div>
            <h3 class="text-white text-xl font-bold uppercase tracking-wider font-orbitron mb-2">
                Визуализация данных
            </h3>
            <p class="text-text-muted text-sm mb-6 max-w-md">
                Сгенерируйте визуальное представление марсианской поверхности на основе выбранного месяца
            </p>
            <button
                @click="handleGenerate"
                class="px-8 py-3 bg-primary-orange/20 hover:bg-primary-orange/30 border border-primary-orange text-primary-orange hover:text-white font-bold uppercase tracking-wide rounded-lg transition-all shadow-lg hover:shadow-primary-orange/20 flex items-center gap-2 cursor-pointer"
            >
                <Sparkles :size="20" />
                Сгенерировать изображение
            </button>
        </div>
    </div>

    <!-- Generated Image Display -->
    <div v-if="generatedImage && showResult" class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg overflow-hidden">
        <div class="bg-primary-orange/20 border-b border-primary-orange/30 p-4 flex items-center gap-3">
            <ImageIcon class="w-6 h-6 text-primary-orange" />
            <h3 class="text-white text-xl font-bold uppercase tracking-wider font-orbitron">
                Сгенерированная визуализация
            </h3>
        </div>
        <div class="p-6">
            <div class="relative rounded-lg overflow-hidden border border-primary-orange/30 mb-4">
                <img
                    :src="generatedImage.image"
                    :alt="generatedImage.interpretation"
                    class="w-full h-auto"
                />
            </div>
            <div class="bg-deep-background/50 rounded-lg p-4 border border-primary-orange/20">
                <h4 class="text-primary-orange text-sm font-bold uppercase tracking-wider mb-3 font-orbitron">
                    Интерпретация ИИ
                </h4>
                <MarkdownRenderer :content="generatedImage.interpretation" />
            </div>
        </div>
    </div>
</template>
