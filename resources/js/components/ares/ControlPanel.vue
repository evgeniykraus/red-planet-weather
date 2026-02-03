<script setup lang="ts">
import { Sun, CloudRain, Moon, Shuffle, Info } from 'lucide-vue-next';
import type { AtmosphereConfig, AtmosphereMode } from '@/composables/useMarsAtmosphere';
import { computed, ref } from 'vue';

interface Props {
    config: AtmosphereConfig;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    update: [config: AtmosphereConfig];
    randomize: [];
}>();

const showTooltip = ref(false);

const dustDensityText = computed(() => {
    const density = props.config.dustDensity;
    if (density > 1600) return 'Критическая';
    if (density > 1000) return 'Высокая';
    if (density > 400) return 'Норма';
    return 'Низкая';
});

const updateWindSpeed = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update', { ...props.config, windSpeed: Number(target.value) });
};

const updateDustDensity = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update', { ...props.config, dustDensity: Number(target.value) });
};

const setMode = (mode: AtmosphereMode) => {
    emit('update', { ...props.config, mode });
};

const handleRandomize = () => {
    emit('randomize');
};
</script>

<template>
    <div class="lg:fixed lg:right-6 lg:top-1/2 lg:-translate-y-1/2 z-20 w-full lg:w-auto px-6 lg:px-0 pb-8 lg:pb-0 max-w-7xl mx-auto lg:mx-0 relative">
        <div class="bg-deep-background/70 backdrop-blur-md border border-primary-orange/30 rounded-lg p-6 w-full lg:w-80 shadow-2xl">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-primary-orange text-xl font-bold tracking-wider uppercase">
                        Параметры среды
                    </h2>
                    <div class="relative">
                        <button 
                            @mouseenter="showTooltip = true" 
                            @mouseleave="showTooltip = false"
                            @click="showTooltip = !showTooltip"
                            class="text-primary-orange hover:text-white transition-colors p-1"
                        >
                            <Info :size="20" />
                        </button>
                        
                        <!-- Tooltip -->
                        <transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 translate-y-1"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 translate-y-1"
                        >
                            <div 
                                v-if="showTooltip"
                                class="absolute right-0 bottom-full mb-2 w-64 bg-deep-background border border-primary-orange/30 rounded-lg p-4 shadow-xl z-30 text-xs text-text-muted leading-relaxed cursor-auto"
                            >
                                <p class="mb-2 font-bold text-white uppercase tracking-wide">О фоне</p>
                                <p>Этот фон генерируется в реальном времени используя HTML5 Canvas. Частицы симулируют разреженную атмосферу Марса.</p>
                            </div>
                        </transition>
                    </div>
                </div>
                <div class="h-0.5 bg-primary-orange/50 mt-2"></div>
            </div>

            <!-- Wind Speed Slider -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-text-muted text-sm font-medium uppercase tracking-wide">
                        Скорость ветра
                    </label>
                    <span class="text-white font-mono text-sm">
                        {{ config.windSpeed }} km/h
                    </span>
                </div>
                <input
                    type="range"
                    :value="config.windSpeed"
                    @input="updateWindSpeed"
                    min="0"
                    max="150"
                    class="w-full h-2 bg-deep-background rounded-lg appearance-none cursor-pointer slider-thumb"
                />
                <div class="flex justify-between text-xs text-text-muted mt-1">
                    <span>0</span>
                    <span>150</span>
                </div>
            </div>

            <!-- Dust Density Slider -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-text-muted text-sm font-medium uppercase tracking-wide">
                        Плотность пыли
                    </label>
                    <span class="text-white font-mono text-sm">
                        {{ dustDensityText }}
                    </span>
                </div>
                <input
                    type="range"
                    :value="config.dustDensity"
                    @input="updateDustDensity"
                    min="100"
                    max="2000"
                    class="w-full h-2 bg-deep-background rounded-lg appearance-none cursor-pointer slider-thumb"
                />
                <div class="flex justify-between text-xs text-text-muted mt-1">
                    <span>100</span>
                    <span>2000</span>
                </div>
            </div>

            <!-- Mode Toggles -->
            <div class="mb-6">
                <label class="text-text-muted text-sm font-medium uppercase tracking-wide mb-3 block">
                    Режим
                </label>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        @click="setMode('day')"
                        :class="[
                            'flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all cursor-pointer',
                            config.mode === 'day'
                                ? 'border-primary-orange bg-primary-orange/20 text-primary-orange'
                                : 'border-primary-orange/30 bg-deep-background/50 text-text-muted hover:border-primary-orange/50'
                        ]"
                    >
                        <Sun :size="20" class="mb-1" />
                        <span class="text-xs font-medium uppercase">День</span>
                    </button>

                    <button
                        @click="setMode('storm')"
                        :class="[
                            'flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all cursor-pointer',
                            config.mode === 'storm'
                                ? 'border-primary-orange bg-primary-orange/20 text-primary-orange'
                                : 'border-primary-orange/30 bg-deep-background/50 text-text-muted hover:border-primary-orange/50'
                        ]"
                    >
                        <CloudRain :size="20" class="mb-1" />
                        <span class="text-xs font-medium uppercase">Буря</span>
                    </button>

                    <button
                        @click="setMode('night')"
                        :class="[
                            'flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all cursor-pointer',
                            config.mode === 'night'
                                ? 'border-primary-orange bg-primary-orange/20 text-primary-orange'
                                : 'border-primary-orange/30 bg-deep-background/50 text-text-muted hover:border-primary-orange/50'
                        ]"
                    >
                        <Moon :size="20" class="mb-1" />
                        <span class="text-xs font-medium uppercase">Ночь</span>
                    </button>
                </div>
            </div>

            <!-- Random Button -->
            <button
                @click="handleRandomize"
                class="w-full py-3 px-4 bg-primary-orange hover:bg-primary-orange/80 text-white font-bold uppercase tracking-wider rounded-lg transition-all flex items-center justify-center gap-2 shadow-lg hover:shadow-xl cursor-pointer"
            >
                <Shuffle :size="20" />
                Случайный день
            </button>
        </div>
    </div>
</template>

<style scoped>
/* Custom slider styling */
.slider-thumb::-webkit-slider-thumb {
    appearance: none;
    width: 16px;
    height: 16px;
    background: #FF4D00;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #1A0904;
    box-shadow: 0 0 10px rgba(255, 77, 0, 0.5);
}

.slider-thumb::-moz-range-thumb {
    width: 16px;
    height: 16px;
    background: #FF4D00;
    border-radius: 50%;
    cursor: pointer;
    border: 2px solid #1A0904;
    box-shadow: 0 0 10px rgba(255, 77, 0, 0.5);
}

.slider-thumb::-webkit-slider-track {
    background: linear-gradient(to right, #FF4D00 0%, #FF4D00 var(--value, 50%), #1A0904 var(--value, 50%), #1A0904 100%);
}
</style>
