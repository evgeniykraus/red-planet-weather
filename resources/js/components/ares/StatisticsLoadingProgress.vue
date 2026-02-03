<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Terminal, Database } from 'lucide-vue-next';

const currentStep = ref(0);
const progress = ref(0);
const currentMessage = ref('');

const steps = [
    { message: '> Подключение к базе данных NASA...', duration: 400 },
    { message: '> Запрос данных марсоход Curiosity...', duration: 500 },
    { message: '> Загрузка телеметрии выбранного месяца...', duration: 600 },
    { message: '> Обработка температурных данных...', duration: 500 },
    { message: '> Вычисление статистических параметров...', duration: 400 },
    { message: '> Формирование отчета...', duration: 300 },
];

let interval: ReturnType<typeof setInterval> | null = null;
let messageTimeout: ReturnType<typeof setTimeout> | null = null;

const typeMessage = (message: string, index = 0) => {
    if (index <= message.length) {
        currentMessage.value = message.slice(0, index);
        messageTimeout = setTimeout(() => typeMessage(message, index + 1), 20);
    }
};

const startProgress = () => {
    const totalDuration = steps.reduce((sum, step) => sum + step.duration, 0);
    const stepDuration = 50; // Update every 50ms
    
    interval = setInterval(() => {
        progress.value += (stepDuration / totalDuration) * 100;
        
        if (progress.value >= 100) {
            progress.value = 100;
            if (interval) clearInterval(interval);
        }
    }, stepDuration);
    
    const showNextStep = (index: number) => {
        if (index < steps.length) {
            currentStep.value = index;
            typeMessage(steps[index].message);
            
            setTimeout(() => {
                showNextStep(index + 1);
            }, steps[index].duration);
        }
    };
    
    showNextStep(0);
};

onMounted(() => {
    startProgress();
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
    if (messageTimeout) clearTimeout(messageTimeout);
});
</script>

<template>
    <div class="bg-card-glass backdrop-blur-md border border-primary-orange/30 rounded-lg p-6">
        <div class="bg-deep-background/90 rounded-lg border border-primary-orange/20 p-6">
            <!-- Terminal Header -->
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-primary-orange/20">
                <Terminal class="w-5 h-5 text-primary-orange" />
                <div class="flex-1">
                    <div class="text-primary-orange text-sm font-bold uppercase tracking-wider font-orbitron">
                        ARES Data Retrieval System
                    </div>
                    <div class="text-text-muted text-xs font-mono">
                        Accessing Curiosity database...
                    </div>
                </div>
                <Database class="w-5 h-5 text-primary-orange animate-pulse" />
            </div>

            <!-- Terminal Output -->
            <div class="space-y-2 mb-6 min-h-[120px] font-mono text-sm">
                <div 
                    v-for="(step, index) in steps.slice(0, currentStep + 1)" 
                    :key="index"
                    class="transition-all duration-300"
                    :class="index === currentStep ? 'text-primary-orange' : 'text-text-muted/60'"
                >
                    <span v-if="index === currentStep">{{ currentMessage }}</span>
                    <span v-else>{{ step.message }}</span>
                    <span v-if="index < currentStep" class="text-green-400 ml-2">[OK]</span>
                    <span 
                        v-if="index === currentStep" 
                        class="inline-block w-2 h-4 bg-primary-orange ml-1 animate-pulse"
                    ></span>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="flex items-center justify-between text-xs font-mono">
                    <span class="text-text-muted">Загрузка данных:</span>
                    <span class="text-primary-orange font-bold">{{ Math.round(progress) }}%</span>
                </div>
                <div class="h-2 bg-deep-background rounded-full overflow-hidden border border-primary-orange/30">
                    <div 
                        class="h-full bg-gradient-to-r from-primary-orange to-yellow-500 transition-all duration-150 ease-out relative"
                        :style="{ width: `${progress}%` }"
                    >
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="mt-4 pt-4 border-t border-primary-orange/20 flex items-center justify-between text-xs font-mono">
                <div class="text-text-muted">
                    Status: <span class="text-green-400">ACTIVE</span>
                </div>
                <div class="text-text-muted">
                    Query Time: <span class="text-blue-400">{{ Math.max(0.1, (3 - (progress / 100) * 3)).toFixed(1) }}s</span>
                </div>
            </div>
        </div>
    </div>
</template>
