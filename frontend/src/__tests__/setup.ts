import { config } from '@vue/test-utils'
import { createI18n } from 'vue-i18n'
import { createRouter, createWebHistory } from 'vue-router'
import { setActivePinia, createPinia } from 'pinia'
import { vi } from 'vitest'

const pinia = createPinia()
setActivePinia(pinia)

const i18n = createI18n({
  legacy: false,
  locale: 'de',
  messages: { de: {} }
})

const router = createRouter({
  history: createWebHistory(),
  routes: []
})

vi.mock('laravel-echo', () => ({
  default: vi.fn().mockImplementation(() => ({
    channel: vi.fn().mockReturnValue({
      listen: vi.fn().mockReturnThis(),
      stopListening: vi.fn().mockReturnThis()
    })
  }))
}))

vi.mock('pusher-js', () => ({}))

vi.stubGlobal('Pusher', vi.fn())

config.global.plugins = [pinia, i18n, router]
config.global.stubs = {
  FavoriteButton: true
}