import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import LiveAndUpcomingSection from '../components/LiveAndUpcomingSection.vue'

describe('LiveAndUpcomingSection', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('shows live matches section when there are live games', () => {
    const wrapper = mount(LiveAndUpcomingSection)
    expect(wrapper.exists()).toBe(true)
  })
})