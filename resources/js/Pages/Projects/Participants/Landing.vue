<template>
    <div class="min-h-screen bg-gray-50">
      <!-- Header Image -->
      <div class="relative w-full h-64 md:h-96">
        <img :src="project.image_url" alt="Event Image" class="w-full h-full object-cover" />
        <div class="absolute top-4 right-4">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-80 text-purple-600">fundoo</span>
        </div>
      </div>
  
      <!-- Main Content -->
      <main class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8 -mt-16">
        <div class="bg-white shadow-lg rounded-lg p-8">
          <!-- Project Header -->
          <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ project.name }}</h1>
          <div class="text-sm text-gray-500 mb-6" v-if="project.date || project.time || project.location">
            <span v-if="project.date || project.time">{{ project.date }} {{ project.time }}</span>
            <span class="mx-2" v-if="(project.date || project.time) && project.location">•</span>
            <span v-if="project.location">@ {{ project.location }}</span>
          </div>
          
          <!-- Project Description -->
          <div class="prose max-w-none mb-8">
            <p class="text-gray-600">{{ project.description }}</p>
            <div class="mt-4" v-if="project.extended_description">
              <p class="text-gray-600">{{ project.extended_description }}</p>
            </div>
          </div>
          
          <!-- Support Section -->
          <div class="mt-10 border-t border-gray-200 pt-8">
            <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your support for {{ participant.first_name }} {{ participant.last_name }}</h2>
            <p class="text-gray-600 mb-6">Your donation will help make this project successful and support our cause.</p>
            
            <!-- Project Images Gallery -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8" v-if="project.gallery_images && project.gallery_images.length">
              <div v-for="(image, index) in project.gallery_images" :key="index" class="rounded-lg overflow-hidden h-40">
                <img :src="image" alt="Project Gallery Image" class="w-full h-full object-cover" />
              </div>
            </div>
            
            <!-- Call to Action -->
            <div class="mt-8 text-center">
              <Link :href="`/projects/${project.id}/participants/${participant.id}/donate`" class="inline-flex items-center px-8 py-4 bg-purple-600 text-white text-lg rounded-lg hover:bg-purple-700 transition duration-200 font-medium shadow-md">
                Donate Now
              </Link>
            </div>
          </div>
        </div>
        
        <!-- Participant Info Card -->
        <div class="mt-8 bg-white shadow-lg rounded-lg p-8">
          <div class="flex items-center mb-6">
            <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center mr-4">
              <span class="text-2xl font-bold text-purple-600">{{ participant.first_name?.[0] || 'P' }}</span>
            </div>
            <div>
              <h3 class="text-xl font-semibold text-gray-900">{{ participant.first_name }} {{ participant.last_name }}</h3>
              <p class="text-gray-600">Project Participant</p>
            </div>
          </div>
          <p class="text-gray-600 mb-4" v-if="participant.bio">{{ participant.bio }}</p>
          <p class="text-gray-600" v-else>Thank you for considering supporting this important cause!</p>
        </div>
      </main>
  
      <!-- Footer -->
      <footer class="bg-gray-100 py-8 mt-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
              <img src="/images/logo.svg" alt="Fundoo Logo" class="h-8" />
            </div>
            <div class="flex space-x-6">
              <a href="mailto:info@fundoo.com" class="text-gray-600 hover:text-purple-600">Contact</a>
              <a href="/privacy" class="text-gray-600 hover:text-purple-600">Privacy Policy</a>
              <a href="/terms" class="text-gray-600 hover:text-purple-600">Terms</a>
            </div>
          </div>
          <div class="mt-8 text-center text-gray-500 text-sm">
            &copy; {{ new Date().getFullYear() }} Fundoo. All rights reserved.
          </div>
        </div>
      </footer>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue'
  import { Link } from '@inertiajs/vue3'
  const props = defineProps({
    project: Object,
    participant: Object,
  })
  </script>