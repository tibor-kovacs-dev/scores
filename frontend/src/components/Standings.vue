<script setup lang="ts">
import { useMatchStore } from '../stores/matchStore'
import { ref, computed, watch, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import { competitions } from '@/data/competitions'

const { t } = useI18n()
const matchStore = useMatchStore()
const route = useRoute()
const router = useRouter()

const selectedCompetition = ref(route.query.competition?.toString() || 'BSA')
const isLoading = ref(false)

const isRealGroup = (group?: string) => {
  if (!group) return false
  const g = group.toUpperCase()
  return g.startsWith('GROUP') || /^[A-Z]$/.test(g)
}

const tables = computed(() => {
  const list = matchStore.standings?.standings || []
  const real = list.filter((g) => isRealGroup(g.group))
  return real.length ? real : list.slice(0, 1)
})

const loadStandings = async (code: string) => {
  isLoading.value = true
  try {
    await matchStore.fetchStandings(code)
  } finally {
    isLoading.value = false
  }
}

watch(selectedCompetition, async (newCode) => {
  router.replace({ query: { ...route.query, competition: newCode } })
  await loadStandings(newCode)
})

onMounted(() => {
  loadStandings(selectedCompetition.value)
})
</script>

<template>
  <div class="bg-card-bg rounded-2xl md:rounded-3xl p-4 md:p-8 border border-border-color">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 md:mb-8 gap-3">
      <h2 class="text-xl md:text-2xl font-bold text-text-primary">
        {{ t('standings') }}
      </h2>

      <select
        v-model="selectedCompetition"
        :disabled="isLoading"
        class="w-full sm:w-auto sm:min-w-52 bg-bg-primary border border-border-color rounded-xl px-4 py-2.5 text-sm text-text-primary focus:outline-none focus:border-emerald-500 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <option v-for="comp in competitions" :key="comp.code" :value="comp.code">
          {{ comp.name }}
        </option>
      </select>
    </div>

    <div v-if="isLoading" class="rounded-2xl border border-border-color p-4 space-y-3">
      <div class="h-5 w-28 rounded bg-border-color/60 animate-pulse" />
      <div v-for="i in 8" :key="i" class="flex items-center gap-3 py-1.5">
        <div class="w-5 h-4 rounded bg-border-color/40 animate-pulse" />
        <div class="w-5 h-5 rounded-full bg-border-color/50 animate-pulse shrink-0" />
        <div class="h-4 flex-1 max-w-28 rounded bg-border-color/50 animate-pulse" />
        <div class="h-4 w-6 rounded bg-border-color/30 animate-pulse ml-auto" />
        <div class="h-4 w-6 rounded bg-border-color/30 animate-pulse hidden sm:block" />
        <div class="h-4 w-8 rounded bg-border-color/40 animate-pulse" />
      </div>
    </div>

    <div v-else-if="tables.length" class="space-y-6 md:space-y-8">
      <div v-for="(groupData, index) in tables" :key="index">
        <h3
          v-if="isRealGroup(groupData.group)"
          class="text-sm md:text-base font-bold text-emerald-500 mb-3"
        >
          {{ groupData.group }}
        </h3>

        <div class="overflow-x-auto overscroll-x-contain rounded-2xl border border-border-color">
          <table class="w-full text-left text-sm min-w-105 border-collapse">
            <thead>
              <tr class="text-[10px] md:text-xs text-muted-text uppercase tracking-wider border-b border-border-color">
                <th class="sticky left-0 z-20 bg-card-bg pl-3 pr-1 py-2.5 font-medium w-7">#</th>
                <th class="sticky left-7 z-20 bg-card-bg px-1.5 py-2.5 font-medium w-22 max-w-22">
                  {{ t('team') }}
                </th>
                <th class="text-center px-1.5 py-2.5 font-medium w-9">{{ t('played') }}</th>
                <th class="text-center px-1.5 py-2.5 font-medium w-8">{{ t('won') }}</th>
                <th class="text-center px-1.5 py-2.5 font-medium w-8">{{ t('draw') }}</th>
                <th class="text-center px-1.5 py-2.5 font-medium w-8">{{ t('lost') }}</th>
                <th class="text-center px-1.5 py-2.5 font-medium w-12">{{ t('goals') }}</th>
                <th class="sticky right-0 z-20 bg-card-bg text-right pl-2 pr-3 py-2.5 font-medium w-11 shadow-[-4px_0_8px_-4px_rgba(0,0,0,0.15)]">
                  {{ t('points') }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(row, idx) in groupData.table || []"
                :key="row.team.id"
                class="border-b border-border-color last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
              >
                <td class="sticky left-0 z-10 bg-card-bg pl-3 pr-1 py-2.5 text-muted-text font-mono text-xs">
                  {{ idx + 1 }}
                </td>
                <td class="sticky left-7 z-10 bg-card-bg px-1.5 py-2.5 w-22 max-w-22">
                  <div class="flex items-center gap-1.5 min-w-0">
                    <img
                      :src="row.team.crest"
                      class="w-5 h-5 object-contain shrink-0"
                      :alt="row.team.name"
                    />
                    <span class="font-medium truncate text-[12px] leading-tight">
                      {{ row.team.shortName || row.team.name }}
                    </span>
                  </div>
                </td>
                <td class="text-center px-1.5 py-2.5 tabular-nums text-text-primary/80">{{ row.playedGames }}</td>
                <td class="text-center px-1.5 py-2.5 tabular-nums text-text-primary/80">{{ row.won }}</td>
                <td class="text-center px-1.5 py-2.5 tabular-nums text-text-primary/80">{{ row.draw }}</td>
                <td class="text-center px-1.5 py-2.5 tabular-nums text-text-primary/80">{{ row.lost }}</td>
                <td class="text-center px-1.5 py-2.5 font-mono text-xs tabular-nums text-text-primary/80">
                  {{ row.goalsFor }}:{{ row.goalsAgainst }}
                </td>
                <td class="sticky right-0 z-10 bg-card-bg text-right pl-2 pr-3 py-2.5 font-bold text-emerald-500 tabular-nums shadow-[-4px_0_8px_-4px_rgba(0,0,0,0.15)]">
                  {{ row.points }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-14 text-muted-text">
      <p class="text-base md:text-lg">{{ t('no_data_available') }}</p>
      <p class="text-sm mt-2 opacity-70">{{ t('try_another_competition') }}</p>
    </div>
  </div>
</template>