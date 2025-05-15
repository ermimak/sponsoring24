<template>
    <div class="min-h-screen bg-gray-100">
      <!-- Header -->
      <header class="bg-gradient-to-r from-purple-600 to-purple-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 class="text-4xl font-bold tracking-tight">{{ project.name }}</h1>
          <p class="mt-3 text-lg opacity-90">{{ project.description }}</p>
        </div>
      </header>
  
      <!-- Main Content -->
      <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg p-8">
          <h2 class="text-3xl font-semibold text-gray-900 mb-6">
            Welcome, {{ participant.first_name }} {{ participant.last_name }}
          </h2>
          <p class="text-gray-600 mb-8">Here’s your personalized dashboard for the project.</p>
  
          <!-- Participant Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
              <h3 class="text-xl font-medium text-gray-900 mb-4">Your Details</h3>
              <dl class="space-y-3">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Email</dt>
                  <dd class="text-gray-900">{{ participant.email }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Role</dt>
                  <dd class="text-gray-900 capitalize">{{ participant.role }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Groups</dt>
                  <dd class="text-gray-900">{{ participant.member_groups.map(g => g.name).join(', ') || 'None' }}</dd>
                </div>
              </dl>
            </div>
            <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
              <h3 class="text-xl font-medium text-gray-900 mb-4">Your Stats</h3>
              <dl class="space-y-3">
                <div>
                  <dt class="text-sm font-medium text-gray-500">Supporters</dt>
                  <dd class="text-gray-900">{{ participant.supporters }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Sales Volume</dt>
                  <dd class="text-gray-900">${{ participant.sales_volume.toFixed(2) }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Emails Sent</dt>
                  <dd class="text-gray-900">{{ participant.emails }}</dd>
                </div>
                <div>
                  <dt class="text-sm font-medium text-gray-500">Landing Page Opened</dt>
                  <dd class="text-gray-900">{{ participant.landing_page_opened ? 'Yes' : 'No' }}</dd>
                </div>
              </dl>
            </div>
          </div>
  
          <!-- Call to Action -->
          <div class="mt-8 text-center">
            <Link :href="`/projects/${project.id}/participants/${participant.id}/donate`" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200 font-medium">
              Support My Efforts - Donate Now
            </Link>
          </div>
        </div>
      </main>
  
      <!-- Footer -->
      <footer class="bg-gray-800 text-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <p>&copy; 2025 {{ project.name }}. All rights reserved.</p>
        </div>
      </footer>
    </div>
  </template>
  
  <script setup>
  import { Link } from '@inertiajs/vue3'
  
  defineProps({
    project: Object,
    participant: Object,
  })
  </script>