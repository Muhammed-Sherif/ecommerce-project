import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

window.Pusher = Pusher

const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'https'
const host = import.meta.env.VITE_REVERB_HOST ?? 'localhost'
const port = Number(import.meta.env.VITE_REVERB_PORT ?? (scheme === 'https' ? 443 : 80))

const echo = new Echo({
  broadcaster: 'reverb',
  key: import.meta.env.VITE_REVERB_APP_KEY,
  wsHost: host,
  wsPort: port,
  wssPort: port,
  forceTLS: scheme === 'https',
  enabledTransports: ['ws', 'wss']
})

export default echo
