<template>
    <div class="relative min-h-screen flex flex-col justify-center items-center bg-[#0a0a16] overflow-hidden">
        <!-- Animated background placeholder -->
        <div class="absolute inset-0 z-0">
            <svg class="w-full h-full"
viewBox="0 0 1920 1080"
fill="none"
xmlns="http://www.w3.org/2000/svg">
                <g>
                    <circle v-for="n in 60"
:key="n"
:cx="Math.random()*1920"
:cy="Math.random()*1080"
:r="Math.random()*6+2"
:fill="`rgba(162,89,255,0.2)`"
class="animated-dot"
:style="{ animationDelay: `${Math.random()*10}s` }" />
                </g>
            </svg>
        </div>

        <!-- Language Switcher -->
        <div class="absolute top-6 right-8 z-10 flex gap-4 text-sm font-medium">
            <a href="/de" class="text-white/70 hover:text-white" :class="{ 'text-purple-400 font-bold': currentLang === 'de' }">DE</a>
            <a href="/fr" class="text-white/70 hover:text-white" :class="{ 'text-purple-400 font-bold': currentLang === 'fr' }">FR</a>
            <a href="/it" class="text-white/70 hover:text-white" :class="{ 'text-purple-400 font-bold': currentLang === 'it' }">IT</a>
        </div>

        <!-- Card -->
        <div class="relative z-10 w-full max-w-md mx-auto">
            <div class="flex flex-col items-center mb-8">
                <!-- Fundoo Logo -->
                <a href="/" class="flex items-center gap-2">
                    <span class="inline-block">
                        <!-- Simple SVG logo placeholder -->
                        <svg width="36"
height="36"
viewBox="0 0 36 36"
fill="none"
xmlns="http://www.w3.org/2000/svg">
                            <circle cx="18"
cy="18"
r="16"
stroke="#a259ff"
stroke-width="4" />
                            <circle cx="18"
cy="18"
r="8"
stroke="#a259ff"
stroke-width="2" />
                        </svg>
                    </span>
                    <span class="text-3xl font-bold text-white tracking-tight">fundoo</span>
                </a>
            </div>
            <div class="bg-white rounded-2xl shadow-xl px-8 py-10">
                <slot></slot>
            </div>
        </div>
        <CookieBanner />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import CookieBanner from '@/Components/CookieBanner.vue';
const currentLang = ref('de'); // TODO: Replace with real i18n logic

defineProps({
    title: {
        type: String,
        required: false
    }
});
</script>

<style scoped>
.animated-dot {
    animation: moveDot 12s linear infinite alternate;
}

@keyframes moveDot {
    0% { transform: translateY(0); }
    100% { transform: translateY(-40px); }
}
</style> 