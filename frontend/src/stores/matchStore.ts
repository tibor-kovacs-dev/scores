import { defineStore } from 'pinia'
import axios from 'axios'
import { getEcho } from '@/lib/echo'
import type { Game, MatchUpdateEvent, StandingsData } from '../types'
import { demoGames } from '@/data/demoGames'

export const useMatchStore = defineStore('match', {
  state: () => ({
    games: [] as Game[],
    standings: null as StandingsData | null,
    loading: false,
    isDemoMode: false,
    demoMessage: '',
  }),

  actions: {
    async fetchGames(date?: string): Promise<void> {
      this.loading = true
      this.isDemoMode = false
      this.demoMessage = ''

      try {
        const params: Record<string, string> = {}

        if (date) {
          params.date = date
        }

        const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/games`, { params })

        const data = Array.isArray(response.data) ? response.data : (response.data?.data ?? [])

        if (data.length === 0) {
          this.games = [...demoGames]
          this.isDemoMode = true
          this.demoMessage = 'Currently, demo data is displayed'
          console.log('Demo mode activated – 0 matches from the API')
        } else {
          this.games = data
          this.isDemoMode = false
          console.log(`${this.games.length} match loaded`)
        }
      } catch (error) {
        console.error('Error loading matches:', error)
        this.games = [...demoGames]
        this.isDemoMode = true
        this.demoMessage = 'Failed to connect to the API. Demo mode is active.'
      } finally {
        this.loading = false
      }
    },

    async fetchStandings(competitionCode?: string) {
      const code = competitionCode || 'PL'

      try {
        const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/standings`, {
          params: { competition: code },
        })
        this.standings = response.data
        console.log(`Standings loaded for: ${code}`)
      } catch (error) {
        console.error(`Standings fetch failed for ${code}:`, error)
        this.standings = null
      }
    },

    listenForUpdates(): () => void {
      const echo = getEcho()
      const channel = echo.channel('matches')

      channel.listen('.match.updated', (e: MatchUpdateEvent) => {
        if (this.isDemoMode) return

        const index = this.games.findIndex((g) => g.id === e.game.id)

        if (index !== -1) {
          this.games.splice(index, 1, e.game)
        } else {
          this.games.push(e.game)
        }
      })

      return () => {
        channel.stopListening('.match.updated')
      }
    },
  },
})
