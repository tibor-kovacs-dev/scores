<script setup lang="ts">
import { computed } from 'vue'
import { useMatchStore } from '../stores/matchStore'
import { useRouter } from 'vue-router'
import MatchCard from './MatchCard.vue'
import { useI18n } from 'vue-i18n'
import type { Game } from '@/types/index.ts'
import { getLeagueName } from '@/data/competitions'
import { useDateFormat } from '@/composables/useDateFormat'

const { t } = useI18n()
const matchStore = useMatchStore()
const router = useRouter()
const { isToday } = useDateFormat()

const games = computed(() => (Array.isArray(matchStore.games) ? matchStore.games : []))

const LIVE_STATUSES = ['IN_PLAY', 'LIVE', 'FIRST_HALF', 'SECOND_HALF', 'HALF_TIME']
const FINISHED = ['FINISHED', 'FT', 'COMPLETED']
const UPCOMING = ['TIMED', 'SCHEDULED']

const liveMatches = computed(() =>
  games.value.filter((g) => LIVE_STATUSES.includes(g.status?.toUpperCase() || '')),
)

const finishedMatches = computed(() =>
  games.value
    .filter((g) => FINISHED.includes(g.status?.toUpperCase() || ''))
    .sort((a, b) => new Date(b.utc_date).getTime() - new Date(a.utc_date).getTime()),
)

const upcomingMatches = computed(() =>
  games.value
    .filter((g) => UPCOMING.includes(g.status?.toUpperCase() || ''))
    .sort((a, b) => new Date(a.utc_date).getTime() - new Date(b.utc_date).getTime()),
)

interface CompetitionGroup {
  code: string
  name: string
  matches: Game[]
}

const groupByCompetition = (matches: Game[]): CompetitionGroup[] => {
  const groups: Record<string, CompetitionGroup> = {}
  matches.forEach((game) => {
    const code = game.competition_code || 'OTHER'
    if (!groups[code]) {
      // Az API-ból mentett teljes nevet preferáljuk; a hardcode-olt lista csak
      // a régi (competition_name nélküli) rekordokhoz kell fallbacknek.
      groups[code] = { code, name: game.competition_name || getLeagueName(code), matches: [] }
    }
    groups[code].matches.push(game)
  })
  return Object.values(groups)
}

const goToStandings = (code: string) => {
  router.push({ path: '/', query: { tab: 'standings', competition: code } })
}
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-12">
    <div v-if="matchStore.isDemoMode && !matchStore.loading"
      class="p-4 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-amber-500 text-sm">
      <p class="font-semibold">{{ t('demo_message') }}</p>
      <p class="mt-1 opacity-80">{{ t('demo_explanation') }}</p>
    </div>

    <div v-if="matchStore.loading" class="space-y-3">
      <div class="h-6 w-40 rounded bg-border-color/60 animate-pulse mb-6" />
      <div v-for="i in 6" :key="i"
        class="w-full max-w-xl mx-auto h-14 rounded-2xl border border-border-color bg-card-bg animate-pulse" />
    </div>

    <template v-else>
      <section v-if="liveMatches.length > 0">
        <div class="flex items-center gap-3 mb-5">
          <span class="relative flex h-3.5 w-3.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-500 opacity-75" />
            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-red-500" />
          </span>
          <h2 class="text-xl font-bold uppercase tracking-wider text-red-500">{{ t('live') }}</h2>
        </div>

        <div v-for="group in groupByCompetition(liveMatches)" :key="group.code" class="mb-8">
          <div @click="goToStandings(group.code)"
            class="text-emerald-400 font-medium mb-2 cursor-pointer hover:underline text-sm">
            {{ group.name }}
          </div>
          <div class="space-y-2">
            <MatchCard v-for="game in group.matches" :key="game.id" :game="game" />
          </div>
        </div>
      </section>

      <section v-if="finishedMatches.length > 0">
        <h2 class="text-lg font-bold uppercase tracking-wider text-emerald-500 mb-5">
          {{ t('finished') }}
        </h2>
        <div v-for="group in groupByCompetition(finishedMatches)" :key="group.code" class="mb-8">
          <div @click="goToStandings(group.code)"
            class="text-emerald-400 font-medium mb-2 cursor-pointer hover:underline text-sm">
            {{ group.name }}
          </div>
          <div class="space-y-2">
            <MatchCard v-for="game in group.matches" :key="game.id" :game="game" :showDate="!isToday(game.utc_date)" />
          </div>
        </div>
      </section>

      <section v-if="upcomingMatches.length > 0">
        <h2 class="text-lg font-bold uppercase tracking-wider text-primary mb-5">
          {{ t('upcoming') }}
        </h2>
        <div v-for="group in groupByCompetition(upcomingMatches)" :key="group.code" class="mb-8">
          <div @click="goToStandings(group.code)"
            class="text-emerald-400 font-medium mb-2 cursor-pointer hover:underline text-sm">
            {{ group.name }}
          </div>
          <div class="space-y-2">
            <MatchCard v-for="game in group.matches" :key="game.id" :game="game" :showDate="!isToday(game.utc_date)" />
          </div>
        </div>
      </section>

      <div v-if="games.length === 0"
        class="text-center py-20 bg-card-bg rounded-2xl border border-dashed border-border-color">
        <p class="text-lg text-muted-text">{{ t('no_matches') }}</p>
      </div>
    </template>
  </div>
</template>