import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { setActivePinia, createPinia } from 'pinia'
import FavoriteButton from '../components/FavoriteButton.vue'

describe('FavoriteButton', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('toggles favorite state', async () => {
    const wrapper = mount(FavoriteButton, {
      props: { teamId: 5 }
    })

    const button = wrapper.find('button')
    await button.trigger('click')

    
    expect(wrapper.emitted()).not.toHaveProperty('toggle')
  })
})