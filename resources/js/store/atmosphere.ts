import { reactive } from 'vue';
import type { AtmosphereConfig } from '@/composables/useMarsAtmosphere';

// Глобальное состояние атмосферы
export const atmosphereStore = reactive<AtmosphereConfig>({
    windSpeed: 50,
    dustDensity: 500, // Значение по умолчанию "Норма"
    mode: 'day',
});

// Функция для обновления состояния
export const updateAtmosphere = (newConfig: Partial<AtmosphereConfig>) => {
    Object.assign(atmosphereStore, newConfig);
};

// Функция для рандомизации
export const randomizeAtmosphere = () => {
    const modes: AtmosphereConfig['mode'][] = ['day', 'storm', 'night'];
    
    atmosphereStore.windSpeed = Math.floor(Math.random() * 100);
    atmosphereStore.dustDensity = Math.floor(Math.random() * 1900) + 100;
    atmosphereStore.mode = modes[Math.floor(Math.random() * 3)];
};
