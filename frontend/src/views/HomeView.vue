<script setup lang="ts">
import { computed, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useMatchStore } from '../stores/matchStore'
import Standings from '../components/Standings.vue'
import TopScorers from '../components/TopScorers.vue'
import SearchBar from '../components/SearchBar.vue'
import Tabs from '../components/Tabs.vue'
import LiveAndUpcomingSection from '../components/LiveAndUpcomingSection.vue'
import TheNavigation from '@/components/TheNavigation.vue'

const { t } = useI18n()
const matchStore = useMatchStore()

const tabs = computed(() => [
  { label: t('upcoming'), component: LiveAndUpcomingSection },
  { label: t('top_scorers'), component: TopScorers },
  { label: t('standings'), component: Standings },
])

let stopListening: (() => void) | null = null

onMounted(() => {
  matchStore.fetchGames()
  stopListening = matchStore.listenForUpdates()
  matchStore.fetchStandings()
})

onUnmounted(() => {
  stopListening?.()
})
</script>

<template>
  <div class="min-h-screen bg-bg-primary text-text-primary font-sans p-4 md:p-8 lg:p-10">
    <div class="max-w-5xl mx-auto">
      <TheNavigation/>
      <SearchBar />
      <Tabs :tabs="tabs" />
    </div>
  </div>
</template>