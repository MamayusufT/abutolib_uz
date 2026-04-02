import process from 'process';      

export default defineNuxtConfig({
  modules: [
    '@nuxt/eslint',
    '@nuxt/ui',
    '@pinia/nuxt',
    '@nuxtjs/i18n',
  ],

  devtools: {
    enabled: false
  },

  i18n: {
    langDir: '../locales', 
    strategy: 'prefix_except_default',
    defaultLocale: 'uz',
    locales: [
      { code: 'uz', iso: 'uz-UZ', file: 'uz.json', name: 'O\'zbekcha', icon: '🇺🇿' },
      { code: 'en', iso: 'en-US', file: 'en.json', name: 'English', icon: '🇺🇸' },
      { code: 'ru', iso: 'ru-RU', file: 'ru.json', name: 'Русский', icon: '🇷🇺' },
    ],
  },

  runtimeConfig: {
    public: {
      siteUrl: process.env.APP_URL || 'https://abutolib.uz/',
      storageBase: process.env.STORAGE_BASE || 'https://api.abutolib.uz',
      apiBase: process.env.API_BASE || 'https://api.abutolib.uz/api',
      siteName: process.env.SITE_NAME || 'Abutolib Test Platformasi',
      reverbKey: process.env.NUXT_PUBLIC_REVERB_KEY || '6d6uwdlvlepvrua552db',
      reverbHost: process.env.NUXT_PUBLIC_REVERB_HOST || 'api.abutolib.uz', 
      reverbPort: process.env.NUXT_PUBLIC_REVERB_PORT || '443', 
      reverbScheme: process.env.NUXT_PUBLIC_REVERB_SCHEME || 'https',
    }
  },

  css: [
    '~/assets/css/main.css',
    'katex/dist/katex.min.css'
  ],

  nitro: {
    prerender: {
      failOnError: false,
      routes: []
    },
    routeRules: {
      '/api/proxy/**': {
        proxy: 'https://ai.abutolib.uz/api/**',
        headers: {
        }
      }
    }
  },

  routeRules: {
    '/': { ssr: true },
    '/admin/**': { ssr: false },
  },

  build: {
    transpile: ['laravel-echo', 'vue','katex']
  },
  
  vite: {
    server: {
      proxy: {
        '/api/quiz': {
          target: 'https://ai.abutolib.uz',
          changeOrigin: true,
          secure: false,
          rewrite: (path) => path
        }
      }
    },
    optimizeDeps: {
      include: ['pusher-js', 'laravel-echo']
    },
    build: {
      commonjsOptions: {
        transformMixedEsModules: true,
      },
    },
    resolve: {
      alias: {
        'pusher-js': 'pusher-js/dist/web/pusher.js'
      }
    }
  },
  
  app: {
    head: {
      titleTemplate: '%s | Test Platformasi',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        { name: 'format-detection', content: 'telephone=no' },
        { name: 'author', content: 'Test Tizimi Jamoasi' }
      ],
      link: [
        { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
      ]
    }
  },

  compatibilityDate: '2025-01-15',

  eslint: {
    config: {
      stylistic: {
        commaDangle: 'never',
        braceStyle: '1tbs'
      }
    }
  }
})