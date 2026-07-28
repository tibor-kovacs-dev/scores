import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import Standings from '../components/Standings.vue'
import { createPinia, setActivePinia } from 'pinia'

vi.mock('../stores/matchStore', () => ({
  useMatchStore: () => ({
    standings: {
      standings: [
        {
          group: 'Group A',
          table: [
            { team: { name: 'Germany', crest: '...' }, playedGames: 3, won: 3, draw: 0, lost: 0, goalDifference: 6, points: 9 }
          ]
        }
      ]
    }
  })
}))

describe('Standings', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('renders group name', () => {
    const wrapper = mount(Standings)
    expect(wrapper.text()).toContain('Group A')
  })

  it('renders team statistics', () => {
    const wrapper = mount(Standings)
    expect(wrapper.text()).toContain('Germany')
    expect(wrapper.text()).toContain('9') // points
  })
})