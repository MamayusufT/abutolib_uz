// plugins/echo.client.ts
import Echo from 'laravel-echo';
import PusherJS from 'pusher-js';

declare global {
  interface Window {
    Pusher: typeof PusherJS;
    Echo: Echo<any>;
  }
}

export default defineNuxtPlugin(() => {
  const runtimeConfig = useRuntimeConfig();
  const config = runtimeConfig.public;

  window.Pusher = PusherJS;

  window.Echo = new Echo({
    broadcaster: 'reverb',
    key: config.reverbKey,
    wsHost: config.reverbHost,
    wsPort: parseInt(config.reverbPort),
    wssPort: parseInt(config.reverbPort),
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
  });
});