<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Terminal, Loader2 } from 'lucide-vue-next';

interface Props {
    isComplete?: boolean;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    finished: [];
}>();

const currentStep = ref(0);
const progress = ref(0);
const currentMessage = ref('');
const isAccelerated = ref(false);

const steps = [
    { message: '> Инициализация связи с марсоходом Curiosity...', duration: 2000, fastDuration: 200 },
    { message: '> Установка защищенного канала передачи данных...', duration: 2500, fastDuration: 200 },
    { message: '> Загрузка телеметрии с Sol #4792...', duration: 3000, fastDuration: 200 },
    { message: '> Анализ атмосферных условий...', duration: 3500, fastDuration: 200 },
    { message: '> Обработка данных температурных сенсоров...', duration: 3000, fastDuration: 200 },
    { message: '> Калибровка визуализации поверхности...', duration: 4000, fastDuration: 200 },
    { message: '> Генерация изображения марсианского ландшафта...', duration: 5000, fastDuration: 200 },
    { message: '> Применение сезонных фильтров...', duration: 3000, fastDuration: 200 },
    { message: '> Рендеринг финального изображения...', duration: 4000, fastDuration: 200 },
    { message: '> Финализация и передача данных...', duration: 2000, fastDuration: 200 },
];

let interval: ReturnType<typeof setInterval> | null = null;
let messageTimeout: ReturnType<typeof setTimeout> | null = null;
let stepTimeouts: Array<ReturnType<typeof setTimeout>> = [];

const typeMessage = (message: string, index = 0, speed = 30) => {
    if (messageTimeout) clearTimeout(messageTimeout);
    
    if (index <= message.length) {
        currentMessage.value = message.slice(0, index);
        messageTimeout = setTimeout(() => typeMessage(message, index + 1, speed), speed);
    }
};

const accelerateProgress = () => {
    isAccelerated.value = true;
    
    // Ускоряем прогресс-бар
    if (interval) clearInterval(interval);
    interval = setInterval(() => {
        progress.value += 5; // Быстрое увеличение
        
        if (progress.value >= 100) {
            progress.value = 100;
            if (interval) clearInterval(interval);
        }
    }, 50);
    
    // Очищаем все запланированные таймауты шагов
    stepTimeouts.forEach(timeout => clearTimeout(timeout));
    stepTimeouts = [];
    
    // Быстро показываем оставшиеся шаги
    const showRemainingStepsFast = (index: number) => {
        if (index < steps.length) {
            currentStep.value = index;
            typeMessage(steps[index].message, 0, 5); // Очень быстрая печать
            
            const timeout = setTimeout(() => {
                showRemainingStepsFast(index + 1);
            }, steps[index].fastDuration);
            
            stepTimeouts.push(timeout);
        } else {
            // Все шаги завершены
            setTimeout(() => {
                emit('finished');
            }, 300);
        }
    };
    
    showRemainingStepsFast(currentStep.value);
};

const startProgress = () => {
    const totalDuration = steps.reduce((sum, step) => sum + step.duration, 0);
    const stepDuration = 100; // Update every 100ms
    
    interval = setInterval(() => {
        progress.value += (stepDuration / totalDuration) * 100;
        
        if (progress.value >= 100) {
            progress.value = 100;
            if (interval) clearInterval(interval);
        }
    }, stepDuration);
    
    const showNextStep = (index: number) => {
        if (index < steps.length && !isAccelerated.value) {
            currentStep.value = index;
            typeMessage(steps[index].message);
            
            const timeout = setTimeout(() => {
                showNextStep(index + 1);
            }, steps[index].duration);
            
            stepTimeouts.push(timeout);
        } else if (index >= steps.length) {
            // Все шаги завершены естественным образом
            emit('finished');
        }
    };
    
    showNextStep(0);
};

// Следим за завершением генерации
watch(() => props.isComplete, (newValue) => {
    if (newValue && !isAccelerated.value) {
        accelerateProgress();
    }
});

onMounted(() => {
    startProgress();
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
    if (messageTimeout) clearTimeout(messageTimeout);
    stepTimeouts.forEach(timeout => clearTimeout(timeout));
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
                        ARES Terminal v2.4.7
                    </div>
                    <div class="text-text-muted text-xs font-mono">
                        Connection: Gale Crater → Earth (RTT: 12.5 min)
                    </div>
                </div>
                <Loader2 class="w-5 h-5 text-primary-orange animate-spin" />
            </div>

            <!-- Terminal Output -->
            <div class="space-y-2 mb-6 min-h-[180px] font-mono text-sm">
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
                    <span class="text-text-muted">Прогресс генерации:</span>
                    <span class="text-primary-orange font-bold">{{ Math.round(progress) }}%</span>
                </div>
                <div class="h-2 bg-deep-background rounded-full overflow-hidden border border-primary-orange/30">
                    <div 
                        class="h-full bg-gradient-to-r from-primary-orange to-red-500 transition-all duration-300 ease-out relative"
                        :style="{ width: `${progress}%` }"
                    >
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
                <div class="text-center text-xs text-text-muted font-mono mt-3">
                    Расчетное время: ~{{ Math.max(1, Math.round(32 - (progress / 100) * 32)) }} сек
                </div>
            </div>

            <!-- System Info -->
            <div class="mt-6 pt-4 border-t border-primary-orange/20 grid grid-cols-3 gap-4 text-xs font-mono">
                <div>
                    <div class="text-text-muted">CPU:</div>
                    <div class="text-green-400">{{ Math.min(95, 45 + Math.round(progress / 2)) }}%</div>
                </div>
                <div>
                    <div class="text-text-muted">Memory:</div>
                    <div class="text-blue-400">{{ Math.min(85, 32 + Math.round(progress / 2)) }}%</div>
                </div>
                <div>
                    <div class="text-text-muted">Network:</div>
                    <div class="text-yellow-400">{{ Math.round(Math.random() * 2 + 8) }} KB/s</div>
                </div>
            </div>
        </div>
    </div>
</template>
