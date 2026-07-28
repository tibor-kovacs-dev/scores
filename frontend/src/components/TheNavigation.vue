<script setup lang="ts">
import { useI18n } from 'vue-i18n'
import { useUIStore } from '../stores/uiStore'
import { useMatchStore } from '../stores/matchStore'
import LanguageSwitcher from './LanguageSwitcher.vue'

const uiStore = useUIStore()
const matchStore = useMatchStore()
const { t } = useI18n()
</script>

<template>
  <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 border-b border-border-color pb-8">
    <div>
      <h1 class="text-4xl md:text-5xl font-black text-text-primary tracking-tighter">
        {{ t('title') }}
      </h1>
      
      <p class="text-emerald-400 text-sm font-medium uppercase tracking-[3px] mt-1">
        {{ t('live_results') }}
      </p>

      <div 
        v-if="matchStore.isDemoMode" 
        class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-amber-500 text-sm font-medium"
      >
        <span>{{ t('demo_message') }}</span>
      </div>
    </div>

    <div class="flex items-center gap-4 mt-4 md:mt-0">
      <LanguageSwitcher />

      <button 
        @click="uiStore.toggleTheme"
        class="p-3 rounded-2xl hover:bg-black/5 dark:hover:bg-white/10 transition-all active:scale-95 text-xl text-text-primary">
        
        <span v-if="uiStore.isDark" class="block w-5 h-5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
          </svg>
        </span>

        <span v-else class="block w-5 h-5">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.752-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
          </svg>
        </span>
      </button>
    </div>
  </header>
</template>