<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import type { Team } from '@/types/index.ts'
import TheNavigation from '@/components/TheNavigation.vue'

const { t } = useI18n()
const route = useRoute()
const team = ref<Team | null>(null)
const loading = ref(true)

const loadTeam = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/teams/${route.params.id}`)
    team.value = res.data?.data ?? res.data
  } catch (e) {
    console.error('Team loading error:', e)
    team.value = null
  } finally {
    loading.value = false
  }
}

onMounted(loadTeam)
</script>

<template>
  <div class="min-h-screen bg-bg-primary text-text-primary font-sans p-4 md:p-8 lg:p-10">
    <div class="max-w-3xl mx-auto">
      <TheNavigation/>
      
      <button 
        @click="$router.back()" 
        class="mb-8 flex items-center gap-2 text-emerald-500 hover:text-text-primary transition-colors">
        ← {{ t('back') }}
      </button>

      <div v-if="loading" class="text-center py-32">
        <div class="animate-spin rounded-full h-14 w-14 border-b-2 border-emerald-500 mx-auto mb-6"></div>
        <p class="text-muted-text">{{ t('loading_team') }}</p>
      </div>

      <div v-else-if="team" class="bg-card-bg rounded-3xl p-8 md:p-10 border border-border-color">
        <div class="flex flex-col md:flex-row items-center gap-8">
          <img :src="team.logo_url" class="w-32 h-32 object-contain" :alt="team.name">
          
          <div class="text-center md:text-left">
            <h1 class="text-4xl font-black text-text-primary">{{ team.name }}</h1>
            <p class="text-emerald-500 text-xl font-bold">{{ team.short_name }}</p>
            <p v-if="team.tla" class="text-muted-text mt-1 font-mono">{{ team.tla }}</p>
          </div>
        </div>

        <div class="mt-12 grid md:grid-cols-2 gap-8">
          <div class="rounded-2xl p-8 border border-border-color bg-black/5 dark:bg-[#0f172a]">
            <h3 class="text-lg font-semibold mb-6 text-emerald-500">{{ t('team_info') }}</h3>
            <div class="space-y-4 text-sm">
              <div class="flex justify-between">
                <span class="text-muted-text">{{ t('full_name') }}</span>
                <span class="text-text-primary font-medium">{{ team.name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-text">{{ t('short_name') }}</span>
                <span class="text-text-primary font-medium">{{ team.short_name }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted-text">TLA</span>
                <span class="font-mono text-text-primary font-medium">{{ team.tla || '-' }}</span>
              </div>
            </div>
          </div>

          <div class="rounded-2xl p-8 border border-border-color bg-black/5 dark:bg-[#0f172a]">
            <h3 class="text-lg font-semibold mb-6 text-emerald-500">{{ t('next_matches') || 'Upcoming matches' }}</h3>
            <p class="text-muted-text italic">{{ t('coming_soon') || 'Can be implemented later' }}</p>
          </div>
        </div>
      </div>

      <div v-else class="text-center py-32">
        <p class="text-2xl text-muted-text">{{ t('team_not_found') }}</p>
      </div>
    </div>
  </div>
</template>