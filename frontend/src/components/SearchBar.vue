<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { debounce } from 'lodash-es'
import { useI18n } from 'vue-i18n'
import type { Game, Team } from '@/types/index.ts'
import MatchCard from './MatchCard.vue'

const { t } = useI18n()
const router = useRouter()

const isGame = (result: Game | Team): result is Game => 'home_team_id' in result

const resultKey = (result: Game | Team) => (isGame(result) ? 'game-' : 'team-') + result.id

const searchQuery = ref('')
const searchResults = ref<(Game | Team)[]>([])

const performSearch = async (q: string) => {
  if (q.length < 2) {
    searchResults.value = []
    return
  }

  try {
    const res = await axios.get<{ teams: Team[]; games: Game[] }>(
      `${import.meta.env.VITE_API_BASE_URL}/search`,
      { params: { q } }
    )
    searchResults.value = [...(res.data.teams || []), ...(res.data.games || [])]
  } catch (e) {
    console.error('Search error:', e)
    searchResults.value = []
  }
}

const debouncedSearch = debounce(performSearch, 250)

watch(searchQuery, (newQuery) => {
  debouncedSearch(newQuery)
})

const goToResult = (result: Game | Team) => {
  if (isGame(result)) {
    router.push(`/match/${result.id}`)
  } else {
    router.push(`/team/${result.id}`)
  }
  searchQuery.value = ''
  searchResults.value = []
}
</script>

<template>
  <div class="relative mb-8">
    <div class="relative">
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="t('search_placeholder')"
        class="w-full bg-card-bg border border-border-color rounded-2xl px-6 py-4 text-lg focus:outline-none focus:border-emerald-500 transition-colors pl-5 text-text-primary"
      />
    </div>

    <div v-if="searchResults.length > 0" class="absolute w-full mt-2 bg-card-bg rounded-2xl border border-border-color shadow-2xl z-50 max-h-96 overflow-auto p-2 space-y-2">
      <template v-for="result in searchResults" :key="resultKey(result)">
        <div v-if="isGame(result)" @click="goToResult(result)">
          <MatchCard :game="result" :showDate="true" />
        </div>

        <div v-else
             @click="goToResult(result)"
             class="px-4 py-3 hover:bg-black/5 dark:hover:bg-white/5 cursor-pointer rounded-2xl border border-border-color flex items-center gap-4">
          <img v-if="result.logo_url"
               :src="result.logo_url"
               class="w-8 h-8 object-contain shrink-0" />
          <p class="font-semibold truncate">{{ result.name }}</p>
        </div>
      </template>
    </div>
  </div>
</template>