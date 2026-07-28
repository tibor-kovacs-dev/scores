<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { competitions } from '@/data/competitions'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const selectedCompetition = ref(route.query.competition?.toString() || 'PL')
const scorersData = ref<any>(null)
const isLoading = ref(false)

const loadTopScorers = async () => {
  isLoading.value = true
  try {
    const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/top-scorers`, {
      params: { competition: selectedCompetition.value },
    })
    scorersData.value = res.data
  } catch (e) {
    console.error('Top scorers load error:', e)
    scorersData.value = null
  } finally {
    isLoading.value = false
  }
}

watch(selectedCompetition, () => {
  router.replace({ query: { ...route.query, competition: selectedCompetition.value } })
  loadTopScorers()
})

onMounted(() => {
  loadTopScorers()
})
</script>

<template>
  <div class="bg-card-bg rounded-3xl p-6 md:p-8 border border-border-color">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <h2 class="text-2xl font-bold text-text-primary">
        {{ t('top_scorers') || 'Top Scorers' }}
      </h2>

      <select
        v-model="selectedCompetition"
        :disabled="isLoading"
        class="bg-bg-primary border border-border-color rounded-xl px-5 py-3 text-text-primary
               focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer min-w-52
               disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <option v-for="comp in competitions" :key="comp.code" :value="comp.code">
          {{ comp.name }}
        </option>
      </select>
    </div>

    <div v-if="isLoading" class="space-y-3">
      <div
        v-for="i in 8"
        :key="i"
        class="flex items-center justify-between rounded-2xl px-5 py-4 border border-transparent"
      >
        <div class="flex items-center gap-4 flex-1">
          <div class="w-8 h-6 rounded bg-border-color/60 animate-pulse" />
          <div class="space-y-2 flex-1 max-w-xs">
            <div class="h-4 w-40 rounded bg-border-color/60 animate-pulse" />
            <div class="h-3 w-24 rounded bg-border-color/40 animate-pulse" />
          </div>
        </div>
        <div class="h-8 w-10 rounded bg-border-color/60 animate-pulse" />
      </div>
    </div>

    <div v-else-if="scorersData?.scorers?.length" class="space-y-2">
      <div
        v-for="(player, index) in scorersData.scorers.slice(0, 15)"
        :key="player.player.id"
        class="flex items-center justify-between rounded-2xl px-5 py-4
               hover:bg-black/5 dark:hover:bg-white/5 transition-all
               border border-transparent hover:border-border-color"
      >
        <div class="flex items-center gap-4 flex-1 min-w-0">
          <span class="text-emerald-500 font-bold text-xl w-8 shrink-0">
            {{ Number(index) + 1 }}.
          </span>
          <div class="min-w-0">
            <p class="font-semibold text-text-primary truncate">{{ player.player.name }}</p>
            <p class="text-sm text-muted-text truncate">{{ player.team.name }}</p>
          </div>
        </div>

        <div class="text-3xl font-black text-emerald-500 tabular-nums pl-4 shrink-0">
          {{ Number(player.goals) }}
        </div>
      </div>
    </div>

    <div v-else class="text-center py-16 text-muted-text">
      <p class="text-lg">{{ t('no_data_available') }}</p>
      <p class="text-sm mt-2 opacity-70">{{ t('try_another_competition') }}</p>
    </div>
  </div>
</template>