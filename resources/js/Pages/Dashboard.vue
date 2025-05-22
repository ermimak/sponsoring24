<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Notifications Panel -->
          <div class="col-span-1">
            <NotificationsPanel />
          </div>

          <!-- Other dashboard content -->
          <div class="col-span-1">
            <!-- Your existing dashboard content -->
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppDashboardLayout.vue';
import NotificationsPanel from '@/Components/NotificationsPanel.vue';
</script> 