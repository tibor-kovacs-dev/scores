import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import axios from 'axios'
import { useMatchStore } from '../stores/matchStore'

vi.mock('axios')

describe('Error handling', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('handles API error gracefully', async () => {
    vi.mocked(axios.get).mockRejectedValue(new Error('Network error'))

    const store = useMatchStore()
    
    await expect(store.fetchGames()).resolves.not.toThrow()
    expect(store.games).toEqual([])
  })
})