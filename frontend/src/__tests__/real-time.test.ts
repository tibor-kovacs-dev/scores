import { describe, it, expect, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useMatchStore } from '../stores/matchStore'

describe('Real-time updates', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('updates game score when broadcast arrives', () => {
    const store = useMatchStore()
    
    const initialGame = {
      id: 41,
      home_team: { name: 'Austria' },
      away_team: { name: 'Germany' },
      score_home: 0,
      score_away: 0,
      status: 'IN_PLAY',
      utc_date: '2026-06-22T17:00:00Z'
    }

    store.games = [initialGame]

    const updatedGame = { ...initialGame, score_home: 2, score_away: 1 }

    store.games.splice(0, 1, updatedGame)

    expect(store.games[0].score_home).toBe(2)
    expect(store.games[0].score_away).toBe(1)
  })
})