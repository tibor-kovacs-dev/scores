<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import type { Game } from '@/types/index.ts'
import TheNavigation from '@/components/TheNavigation.vue'
import { useDateFormat } from '@/composables/useDateFormat'
import { getLeagueName } from '@/data/competitions'

const { t } = useI18n()
const route = useRoute()
const game = ref<Game | null>(null)
const loading = ref(true)
const { formatDateTime } = useDateFormat()

const loadGame = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/games/${route.params.id}`)
    game.value = res.data?.data ?? res.data
  } catch (e) {
    console.error('Betöltési hiba:', e)
    game.value = null
  } finally {
    loading.value = false
  }
}

onMounted(loadGame)

const getStatusText = (g: Game | null) => {
  if (!g) return ''
  
  const status = g.status?.toUpperCase() || ''
  
  if (['IN_PLAY', 'LIVE', 'PAUSED'].includes(status)) {
    return t('live_short') || 'Live'
  }
  
  const map: Record<string, string> = {
    'FINISHED': t('finished') || 'Finished',
    'TIMED': t('timed') || 'Upcoming',
    'POSTPONED': t('postponed') || 'Postponed',
    'CANCELLED': t('cancelled') || 'Cancelled',
    'HALF_TIME': t('half_time') || 'Half-time'
  }
  
  return map[status] || status
}
</script>

<template>
  <div class="min-h-screen bg-bg-primary text-text-primary font-sans p-4 md:p-8 lg:p-10">
    <div class="max-w-7xl mx-auto">
      <TheNavigation/>
      
      <div class="max-w-5xl mx-auto">
        <button 
          @click="$router.back()" 
          class="mb-6 md:mb-8 flex items-center gap-2 text-emerald-400 hover:text-text-primary transition-colors">
          ← {{ t('back') }}
        </button>

        <div v-if="loading" class="text-center py-32">
          <div class="animate-spin rounded-full h-14 w-14 border-b-2 border-emerald-500 mx-auto mb-6"></div>
          <p class="text-muted-text">{{ t('loading_match') }}</p>
        </div>

        <div v-else-if="game && game.home_team && game.away_team" class="space-y-6 md:space-y-10">
          <div class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-10 text-center border border-border-color">
            <div class="flex flex-col md:flex-row justify-center items-center gap-6 md:gap-16">
              <div class="text-center flex-1">
                <img :src="game.home_team.logo_url" class="w-20 h-20 md:w-28 md:h-28 mx-auto mb-2 md:mb-4 object-contain" />
                <h3 class="text-lg md:text-2xl font-bold text-text-primary">{{ game.home_team.name }}</h3>
              </div>

              <div class="text-center my-2 md:my-0">
                <div class="text-5xl md:text-7xl font-black text-text-primary mb-2 md:mb-3 tabular-nums">
                  {{ game.score_home }} - {{ game.score_away }}
                </div>
                <div class="bg-card-bg border border-border-color text-emerald-500 px-4 py-1 md:px-5 md:py-2 rounded-xl font-mono font-bold text-sm md:text-lg">
                  {{ getStatusText(game) }}
                </div>
              </div>

              <div class="text-center flex-1">
                <img :src="game.away_team.logo_url" class="w-20 h-20 md:w-28 md:h-28 mx-auto mb-2 md:mb-4 object-contain" />
                <h3 class="text-lg md:text-2xl font-bold text-text-primary">{{ game.away_team.name }}</h3>
              </div>
            </div>

            <p class="mt-6 md:mt-8 text-muted-text text-sm md:text-lg">
              {{ formatDateTime(game.utc_date) }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
            <div class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-8 border border-border-color">
              <h3 class="text-lg md:text-xl font-semibold mb-4 md:mb-6 text-text-primary">{{ t('match_info') }}</h3>
              <div class="space-y-3 md:space-y-4">
                <div class="flex justify-between py-1 md:py-2 border-b border-border-color">
                  <span class="text-sm md:text-base text-muted-text">{{ t('competition') }}</span>
                  <span class="text-sm md:text-base font-medium text-text-primary">{{ game.competition_name || getLeagueName(game.competition_code || '') || game.competition_code || '-' }}</span>
                </div>
                <div class="flex justify-between py-1 md:py-2 border-b border-border-color">
                  <span class="text-sm md:text-base text-muted-text">{{ t('status') }}</span>
                  <span class="text-sm md:text-base font-medium text-text-primary">{{ getStatusText(game) }}</span>
                </div>
                <div class="flex justify-between py-1 md:py-2">
                  <span class="text-sm md:text-base text-muted-text">{{ t('total_goals') }}</span>
                  <span class="text-sm md:text-base font-medium text-emerald-400">
                    {{ (game.score_home ?? 0) + (game.score_away ?? 0) }}
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-8 border border-border-color flex flex-col justify-center items-center text-center">
              <h3 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 text-text-primary">{{ t('information') }}</h3>
              <p class="text-sm md:text-base text-muted-text italic">
                {{ t('api_limit_info') }}
              </p>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-32">
          <p class="text-2xl text-muted-text">{{ t('match_not_found') }}</p>
        </div>
      </div>
    </div>
  </div>
</template>