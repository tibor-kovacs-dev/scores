<script setup lang="ts">
import { useI18n } from 'vue-i18n'

const { locale } = useI18n()

const switchLanguage = (lang: 'de' | 'hu') => {
  locale.value = lang
  localStorage.setItem('locale', lang)
}
</script>

<template>
  <div class="flex gap-1 bg-card-bg rounded-xl p-1 border border-border-color">
    <button 
      @click="switchLanguage('de')"
      :class="{ 'bg-emerald-500 text-white shadow-sm': locale === 'de' }"
      class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all text-text-primary">
      DE
    </button>
    <button 
      @click="switchLanguage('hu')"
      :class="{ 'bg-emerald-500 text-white shadow-sm': locale === 'hu' }"
      class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all text-text-primary">
      HU
    </button>
  </div>
</template>