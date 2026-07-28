import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import { useMatchStore } from '../stores/matchStore'
import axios from 'axios'

vi.mock('axios')

describe('matchStore', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('fetches games successfully', async () => {
    const mockGames = [
      { id: 1, home_team: { name: 'Austria' }, away_team: { name: 'Germany' }, status: 'FINISHED' }
    ]

    vi.mocked(axios.get).mockResolvedValue({ data: mockGames })

    const store = useMatchStore()
    await store.fetchGames()

    expect(store.games).toHaveLength(1)
    expect(store.games[0].home_team.name).toBe('Austria')
  })

  it('toggles favorites correctly', () => {
    const store = useMatchStore()
    
    store.toggleFavorite(5)
    expect(store.favorites).toContain(5)

    store.toggleFavorite(5)
    expect(store.favorites).not.toContain(5)
  })
})