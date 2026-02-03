import { onMounted, onUnmounted, watch, type Ref } from 'vue';
import { atmosphereStore, updateAtmosphere, randomizeAtmosphere } from '@/store/atmosphere';

export type AtmosphereMode = 'day' | 'storm' | 'night';

export interface AtmosphereConfig {
    windSpeed: number;
    dustDensity: number;
    mode: AtmosphereMode;
}

interface Theme {
    baseColor: { r: number; g: number; b: number };
    skyGradient: string[];
    starOpacity: number;
    verticalDrift: number;
}

const themes: Record<AtmosphereMode, Theme> = {
    day: {
        baseColor: { r: 234, g: 88, b: 12 }, // Brighter Orange
        skyGradient: ['#2a1005', '#7c2d12'], // Dark brown to rust
        starOpacity: 0.1, // Stars barely visible
        verticalDrift: 0.3
    },
    storm: {
        baseColor: { r: 153, g: 27, b: 27 }, // Red-800
        skyGradient: ['#250505', '#450a0a'], // Very dark red
        starOpacity: 0.0, // No stars
        verticalDrift: 0.8 // Chaos
    },
    night: {
        baseColor: { r: 100, g: 116, b: 139 }, // Slate blueish dust
        skyGradient: ['#020617', '#0f172a'], // Black to dark blue
        starOpacity: 1.0, // Stars clear
        verticalDrift: 0.1 // Calm
    }
};

class Star {
    x: number;
    y: number;
    size: number;
    opacity: number;
    twinkleSpeed: number;
    canvas: HTMLCanvasElement;

    constructor(canvas: HTMLCanvasElement) {
        this.canvas = canvas;
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 1.5;
        this.opacity = Math.random();
        this.twinkleSpeed = Math.random() * 0.02 + 0.005;
    }

    draw(ctx: CanvasRenderingContext2D, globalStarOpacity: number) {
        if (globalStarOpacity <= 0.05) return;
        
        // Twinkle effect
        this.opacity += this.twinkleSpeed;
        if (this.opacity > 1 || this.opacity < 0.2) {
            this.twinkleSpeed = -this.twinkleSpeed;
        }

        ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity * globalStarOpacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

class Particle {
    x: number;
    y: number;
    z: number;
    size: number;
    speedX: number;
    speedY: number;
    opacity: number;
    canvas: HTMLCanvasElement;

    constructor(canvas: HTMLCanvasElement) {
        this.canvas = canvas;
        this.x = 0;
        this.y = 0;
        this.z = 0;
        this.size = 0;
        this.speedX = 0;
        this.speedY = 0;
        this.opacity = 0;
        this.reset(true);
    }

    reset(randomX = false) {
        this.x = randomX ? Math.random() * this.canvas.width : -10;
        this.y = Math.random() * this.canvas.height;
        
        // Parallax depth: larger particles are "closer" and move faster
        this.z = Math.random() * 3 + 1; // 1 to 4
        this.size = Math.random() * 1.5 * this.z; 
        
        // Speed depends on Z (depth)
        this.speedX = (Math.random() * 0.5 + 0.5) * this.z * 0.5; 
        this.speedY = (Math.random() - 0.5) * 0.2; // Slight native drift
        
        this.opacity = Math.random() * 0.5 + 0.1;
    }

    update(windSpeed: number, turbulance: number, verticalDrift: number) {
        // Apply global wind speed
        this.x += this.speedX * windSpeed;
        
        // Apply vertical drift/turbulence
        this.y += this.speedY + (Math.sin(Date.now() * 0.001 * turbulance + this.x) * verticalDrift);

        // Reset if off screen
        if (this.x > this.canvas.width + 20) {
            this.reset(false);
        }
        if (this.y > this.canvas.height || this.y < -20) {
            this.y = Math.random() * this.canvas.height;
        }
    }

    draw(ctx: CanvasRenderingContext2D, baseColor: { r: number, g: number, b: number }) {
        ctx.fillStyle = `rgba(${baseColor.r}, ${baseColor.g}, ${baseColor.b}, ${this.opacity})`;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
    }
}

class MarsAtmosphereEffect {
    canvas: HTMLCanvasElement;
    ctx: CanvasRenderingContext2D;
    particles: Particle[] = [];
    stars: Star[] = [];
    animationFrameId: number | null = null;
    
    constructor(canvas: HTMLCanvasElement, ctx: CanvasRenderingContext2D) {
        this.canvas = canvas;
        this.ctx = ctx;
    }

    initializeParticles(count: number) {
        // Adjust array size intelligently
        if (count > this.particles.length) {
            for (let i = this.particles.length; i < count; i++) {
                this.particles.push(new Particle(this.canvas));
            }
        } else {
            this.particles = this.particles.slice(0, count);
        }
    }

    initializeStars() {
        this.stars = [];
        for (let i = 0; i < 200; i++) {
            this.stars.push(new Star(this.canvas));
        }
    }

    draw(config: AtmosphereConfig) {
        const theme = themes[config.mode];
        const width = this.canvas.width;
        const height = this.canvas.height;

        // Clear and draw gradient background
        const gradient = this.ctx.createLinearGradient(0, height, 0, 0);
        gradient.addColorStop(0, theme.skyGradient[0]);
        gradient.addColorStop(1, theme.skyGradient[1]);
        
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, width, height);

        // Draw Stars
        this.stars.forEach(star => star.draw(this.ctx, theme.starOpacity));

        // Calculate wind speed multiplier (0-150 slider -> 0-6 multiplier)
        const windMultiplier = config.windSpeed / 25;
        const turbulance = config.windSpeed / 50;

        // Draw Dust
        this.particles.forEach(p => {
            p.update(windMultiplier, turbulance, theme.verticalDrift);
            p.draw(this.ctx, theme.baseColor);
        });

        // Draw a subtle ground texture/haze at the bottom
        const groundHaze = this.ctx.createLinearGradient(0, height, 0, height * 0.8);
        groundHaze.addColorStop(0, `rgba(${theme.baseColor.r}, ${theme.baseColor.g}, ${theme.baseColor.b}, 0.4)`);
        groundHaze.addColorStop(1, 'transparent');
        this.ctx.fillStyle = groundHaze;
        this.ctx.fillRect(0, height * 0.8, width, height * 0.2);
    }

    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        
        // Re-init stars to cover new area
        this.initializeStars();
        // Particles handle their own reset on update if out of bounds, but we can force reset if needed
        // For now, let them drift naturally or reset when off screen
    }
}

export function useMarsAtmosphere(
    canvasRef: Ref<HTMLCanvasElement | null>
) {
    let effect: MarsAtmosphereEffect | null = null;
    let resizeTimeout: number | null = null;

    const animate = () => {
        if (!effect) return;

        effect.draw(atmosphereStore);
        effect.animationFrameId = requestAnimationFrame(animate);
    };

    const handleResize = () => {
        if (resizeTimeout) {
            clearTimeout(resizeTimeout);
        }
        
        resizeTimeout = window.setTimeout(() => {
            if (effect) {
                effect.resize();
            }
        }, 100);
    };

    // Watch for dust density changes to update particle count
    watch(
        () => atmosphereStore.dustDensity,
        (newDensity) => {
            if (effect) {
                const count = newDensity < 100 ? newDensity * 10 : newDensity;
                effect.initializeParticles(count);
            }
        }
    );

    // Watch for mode changes to adjust slider defaults if needed
    watch(
        () => atmosphereStore.mode,
        (newMode) => {
            if (newMode === 'storm') {
                updateAtmosphere({ windSpeed: 120, dustDensity: 1500 });
            } else if (newMode === 'night') {
                updateAtmosphere({ windSpeed: 10, dustDensity: 300 });
            } else {
                updateAtmosphere({ windSpeed: 50, dustDensity: 500 });
            }
        }
    );

    onMounted(() => {
        const canvas = canvasRef.value;
        if (!canvas) return;

        const ctx = canvas.getContext('2d', { 
            alpha: false, // We draw full background now
            desynchronized: true
        });
        if (!ctx) return;

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        effect = new MarsAtmosphereEffect(canvas, ctx);
        effect.initializeStars();
        effect.initializeParticles(atmosphereStore.dustDensity);

        animate();

        window.addEventListener('resize', handleResize);
    });

    onUnmounted(() => {
        if (effect && effect.animationFrameId !== null) {
            cancelAnimationFrame(effect.animationFrameId);
        }
        window.removeEventListener('resize', handleResize);
        
        if (resizeTimeout) {
            clearTimeout(resizeTimeout);
        }
    });

    return {
        atmosphereStore,
        updateAtmosphere,
        randomizeAtmosphere
    };
}
