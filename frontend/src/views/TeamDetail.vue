<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import type { Game, TeamDetail } from '@/types/index.ts'
import TheNavigation from '@/components/TheNavigation.vue'
import { useDateFormat } from '@/composables/useDateFormat'
import { getLeagueName } from '@/data/competitions'

const { t } = useI18n()
const route = useRoute()
const team = ref<TeamDetail | null>(null)
const games = ref<Game[]>([])
const loading = ref(true)
const { formatDateTime } = useDateFormat()

const loadTeam = async () => {
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/teams/${route.params.id}`)
    team.value = res.data?.data ?? res.data
    games.value = team.value?.games ?? []
  } catch (e) {
    console.error('Betöltési hiba:', e)
    team.value = null
    games.value = []
  } finally {
    loading.value = false
  }
}

onMounted(loadTeam)

const getStatusText = (g: Game | null) => {
  if (!g) return ''

  const status = g.status?.toUpperCase() || ''

  if (['IN_PLAY', 'LIVE', 'PAUSED'].includes(status)) {
    return t('live_short') || 'Live'
  }

  const map: Record<string, string> = {
    FINISHED: t('finished') || 'Finished',
    TIMED: t('timed') || 'Upcoming',
    POSTPONED: t('postponed') || 'Postponed',
    CANCELLED: t('cancelled') || 'Cancelled',
    HALF_TIME: t('half_time') || 'Half-time'
  }

  return map[status] || status
}

const getOpponent = (game: Game) => {
  if (!team.value) return '-'

  if (game.home_team?.id === team.value.id) {
    return game.away_team?.name || '-'
  }

  return game.home_team?.name || '-'
}

const isHomeTeam = (game: Game) => {
  return game.home_team?.id === team.value?.id
}

const getTeamScore = (game: Game) => {
  return isHomeTeam(game) ? game.score_home : game.score_away
}

const getOpponentScore = (game: Game) => {
  return isHomeTeam(game) ? game.score_away : game.score_home
}
</script>

<template>
  <div class="min-h-screen bg-bg-primary text-text-primary font-sans p-4 md:p-8 lg:p-10">
    <div class="max-w-7xl mx-auto">
      <TheNavigation />

      <div class="max-w-5xl mx-auto">
        <button
          @click="$router.back()"
          class="mb-6 md:mb-8 flex items-center gap-2 text-emerald-400 hover:text-text-primary transition-colors"
        >
          ← {{ t('back') }}
        </button>

        <div v-if="loading" class="text-center py-32">
          <div class="animate-spin rounded-full h-14 w-14 border-b-2 border-emerald-500 mx-auto mb-6"></div>
          <p class="text-muted-text">{{ t('loading') }}</p>
        </div>

        <div v-else-if="team" class="space-y-6 md:space-y-10">
          <div class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-10 text-center border border-border-color">
            <img
              v-if="team.logo_url"
              :src="team.logo_url"
              :alt="team.name"
              class="w-24 h-24 md:w-32 md:h-32 mx-auto mb-4 md:mb-6 object-contain"
            />

            <h1 class="text-3xl md:text-5xl font-black text-text-primary">
              {{ team.name }}
            </h1>

            <p
              v-if="team.short_name"
              class="mt-2 text-muted-text text-sm md:text-base"
            >
              {{ team.short_name }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
            <div class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-8 border border-border-color">
              <h2 class="text-lg md:text-xl font-semibold mb-4 md:mb-6 text-text-primary">
                {{ t('team_info') }}
              </h2>

              <div class="space-y-3 md:space-y-4">
                <div class="flex justify-between py-1 md:py-2 border-b border-border-color">
                  <span class="text-sm md:text-base text-muted-text">
                    {{ t('team_name') }}
                  </span>
                  <span class="text-sm md:text-base font-medium text-text-primary">
                    {{ team.name }}
                  </span>
                </div>

                <div
                  v-if="team.short_name"
                  class="flex justify-between py-1 md:py-2 border-b border-border-color"
                >
                  <span class="text-sm md:text-base text-muted-text">
                    {{ t('short_name') }}
                  </span>
                  <span class="text-sm md:text-base font-medium text-text-primary">
                    {{ team.short_name }}
                  </span>
                </div>

                <div class="flex justify-between py-1 md:py-2">
                  <span class="text-sm md:text-base text-muted-text">
                    {{ t('matches') }}
                  </span>
                  <span class="text-sm md:text-base font-medium text-emerald-400">
                    {{ games.length }}
                  </span>
                </div>
              </div>
            </div>

            <div class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-8 border border-border-color flex flex-col justify-center items-center text-center">
              <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 text-text-primary">
                {{ t('information') }}
              </h2>

              <p class="text-sm md:text-base text-muted-text italic">
                {{ t('api_limit_info') }}
              </p>
            </div>
          </div>

          <div
            v-if="games.length"
            class="bg-card-bg rounded-2xl md:rounded-3xl p-6 md:p-8 border border-border-color"
          >
            <h2 class="text-lg md:text-xl font-semibold mb-4 md:mb-6 text-text-primary">
              {{ t('matches') }}
            </h2>

            <div class="space-y-3">
              <router-link
                v-for="game in games"
                :key="game.id"
                :to="`/game/${game.id}`"
                class="block rounded-xl border border-border-color p-4 hover:border-emerald-500/50 transition-colors"
              >
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                  <div class="flex items-center gap-4">
                    <img
                      v-if="isHomeTeam(game) ? game.away_team?.logo_url : game.home_team?.logo_url"
                      :src="isHomeTeam(game) ? game.away_team?.logo_url : game.home_team?.logo_url"
                      :alt="getOpponent(game)"
                      class="w-10 h-10 object-contain"
                    />

                    <div>
                      <p class="font-semibold text-text-primary">
                        {{ getOpponent(game) }}
                      </p>

                      <p class="text-xs md:text-sm text-muted-text">
                        {{ getLeagueName(game.competition_code || '') || game.competition_name || game.competition_code || '-' }}
                      </p>
                    </div>
                  </div>

                  <div class="flex items-center justify-between md:justify-end gap-5">
                    <div class="text-sm text-muted-text">
                      {{ formatDateTime(game.utc_date) }}
                    </div>

                    <div class="text-lg font-bold tabular-nums text-text-primary">
                      {{ getTeamScore(game) ?? '-' }} - {{ getOpponentScore(game) ?? '-' }}
                    </div>

                    <div class="text-xs font-semibold text-emerald-400">
                      {{ getStatusText(game) }}
                    </div>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-32">
          <p class="text-2xl text-muted-text">
            {{ t('team_not_found') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>