import { describe, it, expect, vi, beforeEach } from 'vitest'
import { setActivePinia, createPinia } from 'pinia'
import axios from 'axios'
import { useMatchStore } from '../stores/matchStore'

vi.mock('axios')

describe('API Integration', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('fetches standings successfully', async () => {
    const mockStandings = { standings: [{ group: 'Group A', table: [] }] }
    vi.mocked(axios.get).mockResolvedValue({ data: mockStandings })

    const store = useMatchStore()
    await store.fetchStandings()

    expect(store.standings).toEqual(mockStandings)
  })

  it('handles 404 gracefully', async () => {
    vi.mocked(axios.get).mockRejectedValue({ response: { status: 404 } })

    const store = useMatchStore()
    await expect(store.fetchStandings()).resolves.not.toThrow()
  })
})