<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import type { Game } from '@/types/index.ts'
import { useDateFormat } from '@/composables/useDateFormat'

const { t } = useI18n()
const router = useRouter()

const props = defineProps<{
  game: Game
  showDate?: boolean
}>()

const { formatTime, formatDate } = useDateFormat()

const goToMatch = () => {
  router.push(`/match/${props.game.id}`)
}

const status = computed(() => props.game.status?.toUpperCase() || '')

const isLive = computed(() =>
  ['IN_PLAY', 'LIVE', 'FIRST_HALF', 'SECOND_HALF', 'HALF_TIME'].includes(status.value)
)

const isFinished = computed(() =>
  ['FINISHED', 'FT', 'COMPLETED'].includes(status.value)
)
</script>

<template>
  <div @click="goToMatch" class="w-full mx-auto bg-card-bg border border-border-color rounded-2xl 
           px-6 py-2.5 hover:border-emerald-500/40 hover:bg-black/5 dark:hover:bg-white/5 
           transition-all cursor-pointer flex items-center gap-3 text-[13px]">

    <div class="flex items-center gap-3 flex-1 min-w-0">
      <img :src="game.home_team?.logo_url" class="w-6 h-6 object-contain shrink-0" :alt="game.home_team?.name || ''" />
      <span class="font-medium truncate">
        {{ game.home_team?.short_name || game.home_team?.tla || game.home_team?.name?.substring(0, 10) || '?' }}
      </span>
    </div>

    <div class="flex flex-col items-center shrink-0 w-16">
      <div v-if="isLive || isFinished" class="text-base font-bold tabular-nums">
        {{ game.score_home }} - {{ game.score_away }}
      </div>
      <div v-else class="text-emerald-500 font-mono font-semibold text-sm">
        {{ formatTime(game.utc_date) }}
      </div>

      <div v-if="props.showDate" class="text-[9px] text-muted-text mt-px">
        {{ formatDate(game.utc_date) }}
      </div>

      <div v-if="isLive" class="text-[9px] font-bold mt-px animate-pulse">
        <span v-if="status === 'HALF_TIME'" class="text-amber-500">⏸️ {{ t('half_time') }}</span>
        <span v-else class="text-red-500">● LIVE</span>
      </div>
      <div v-else-if="isFinished" class="text-[9px] text-muted-text mt-px">
        {{ t('finished') || 'Vége' }}
      </div>
    </div>

    <div class="flex items-center gap-2 flex-1 min-w-0 justify-end">
      <span class="font-medium truncate text-right">
        {{ game.away_team?.short_name || game.away_team?.tla || game.away_team?.name?.substring(0, 10) || '?' }}
      </span>
      <img :src="game.away_team?.logo_url" class="w-6 h-6 object-contain shrink-0" :alt="game.away_team?.name || ''" />
    </div>
  </div>
</template>