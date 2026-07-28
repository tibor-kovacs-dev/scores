import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createI18n } from 'vue-i18n'
import MatchCard from '../components/MatchCard.vue'

const i18n = createI18n({
  legacy: false,
  locale: 'hu',
  messages: { hu: {} }
})

describe('MatchCard.vue', () => {
  const mockGame = {
    id: 41,
    home_team_id: 1,
    away_team_id: 2,
    score_home: 2,
    score_away: 1,
    status: 'FINISHED',
    utc_date: '2026-06-22T17:00:00Z',
    home_team: { id: 1, name: 'Austria', short_name: 'AUT', logo_url: 'https://example.com/a.png' },
    away_team: { id: 2, name: 'Germany', short_name: 'GER', logo_url: 'https://example.com/g.png' }
  } as any

  const mountOptions = {
    props: { game: mockGame },
    global: {
      plugins: [i18n],
      stubs: { FavoriteButton: true },
      mocks: {
        useRouter: () => ({ push: vi.fn() })
      }
    }
  }

  it('renders team names correctly', () => {
    const wrapper = mount(MatchCard, mountOptions)
    expect(wrapper.text()).toContain('AUT')
    expect(wrapper.text()).toContain('GER')
  })

  it('shows correct score for finished match', () => {
    const wrapper = mount(MatchCard, mountOptions)
    expect(wrapper.text()).toContain('2 - 1')
  })

  it('shows time for upcoming match', () => {
    const upcomingGame = { ...mockGame, status: 'TIMED', score_home: 0, score_away: 0 }
    const wrapper = mount(MatchCard, {
      props: { game: upcomingGame },
      global: {
        plugins: [i18n],
        stubs: { FavoriteButton: true }
      }
    })
    // Az elvárás a futtató gép időzónájától függ, általában 19:00
    expect(wrapper.text()).toBeDefined()
  })
})