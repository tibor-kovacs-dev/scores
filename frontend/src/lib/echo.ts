import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

let echo: InstanceType<typeof Echo> | null = null

export function getEcho() {
  if (!echo) {
    window.Pusher = Pusher

    echo = new Echo({
      broadcaster: 'reverb',
      key: import.meta.env.VITE_REVERB_APP_KEY,
      wsHost: import.meta.env.VITE_REVERB_HOST,
      wsPort: Number(import.meta.env.VITE_REVERB_PORT),
      wssPort: Number(import.meta.env.VITE_REVERB_PORT),
      forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
      enabledTransports: ['ws', 'wss'],
    })
  }

  return echo
}