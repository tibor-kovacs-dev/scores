import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import MatchCard from '../components/MatchCard.vue'

describe('Snapshot tests', () => {
  it('MatchCard matches snapshot', () => {
    const wrapper = mount(MatchCard, {
      props: {
        game: {
          id: 1,
          home_team: { name: 'Austria', short_name: 'AUT', logo_url: '' },
          away_team: { name: 'Germany', short_name: 'GER', logo_url: '' },
          score_home: 1,
          score_away: 1,
          status: 'FINISHED',
          utc_date: '2026-06-22T17:00:00Z'
        }
      }
    })

    expect(wrapper.html()).toMatchSnapshot()
  })
})