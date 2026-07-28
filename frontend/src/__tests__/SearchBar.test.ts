import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createI18n } from 'vue-i18n'
import SearchBar from '../components/SearchBar.vue'

const i18n = createI18n({
  legacy: false,
  locale: 'de',
  messages: { de: { search_placeholder: 'Suche nach Team oder Spiel...' } }
})

vi.mock('axios', () => ({
  default: {
    get: vi.fn().mockResolvedValue({
      data: { teams: [], games: [] }
    })
  }
}))

describe('SearchBar', () => {
  it('renders search input', () => {
    const wrapper = mount(SearchBar, {
      global: { plugins: [i18n] }
    })
    expect(wrapper.find('input').exists()).toBe(true)
  })

  it('calls search on input (debounced)', async () => {
    const wrapper = mount(SearchBar, {
      global: { plugins: [i18n] }
    })

    const input = wrapper.find('input')
    await input.setValue('Austria')

    await new Promise(resolve => setTimeout(resolve, 300))

    expect(wrapper.vm.searchResults).toBeDefined()
  })
})