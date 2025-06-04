<template>
    <Head :title="title" />
    <div class="relative min-h-screen flex flex-col justify-center items-center bg-[#0a0a16] overflow-hidden px-4 sm:px-6 lg:px-8">
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
        <div class="absolute top-6 right-6 sm:right-8 z-10 flex gap-3 sm:gap-4 text-sm font-medium">
            <a href="/de" class="text-white/70 hover:text-white transition-colors" :class="{ 'text-purple-400 font-bold': currentLang === 'de' }">DE</a>
            <a href="/fr" class="text-white/70 hover:text-white transition-colors" :class="{ 'text-purple-400 font-bold': currentLang === 'fr' }">FR</a>
            <a href="/it" class="text-white/70 hover:text-white transition-colors" :class="{ 'text-purple-400 font-bold': currentLang === 'it' }">IT</a>
        </div>

        <!-- Content Area -->
        <div class="relative z-10 w-full max-w-md mx-auto">
            <div class="flex flex-col items-center mb-8">
                <!-- Sponsoring24 Logo -->
                <a href="/" class="flex items-center gap-3">
                    <span>
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="s24IconGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#A855F7;stop-opacity:1" /> <!-- Purple-500 -->
                                    <stop offset="100%" style="stop-color:#3B82F6;stop-opacity:1" /> <!-- Blue-500 -->
                                </linearGradient>
                            </defs>
                            <rect width="40" height="40" rx="8" fill="url(#s24IconGradient)"/>
                            <text x="50%" y="50%" dy=".1em" dominant-baseline="middle" text-anchor="middle" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="white">
                                S24
                            </text>
                        </svg>
                    </span>
                    <span class="text-3xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
                        Sponsoring24
                    </span>
                </a>
            </div>
            <!-- Slot for Login/Register card -->
            <slot></slot>
        </div>
        <CookieBanner />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
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