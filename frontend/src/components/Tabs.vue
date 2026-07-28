<script setup lang="ts">
import { ref } from 'vue'
import type { Component } from 'vue'

defineProps<{
  tabs: Array<{
    label: string
    component: Component
  }>
}>()

const activeTab = ref(0)
</script>

<template>
  <div class="mb-8">
    <div class="flex border-b border-border-color overflow-x-auto pb-1 snap-x snap-mandatory scrollbar-hide">
      <button
        v-for="(tab, index) in tabs"
        :key="index"
        @click="activeTab = index"
        class="px-8 py-4 font-medium text-sm uppercase tracking-widest transition-colors border-b-2 -mb-px whitespace-nowrap snap-start"
        :class="{
          'border-emerald-500 text-emerald-400': activeTab === index,
          'border-transparent text-muted-text hover:text-text-primary': activeTab !== index
        }">
        {{ tab.label }}
      </button>
    </div>

    <div class="pt-8">
      <component :is="tabs[activeTab]?.component" />
    </div>
  </div>
</template>