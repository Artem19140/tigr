import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg'
import { ru } from 'vuetify/locale'
import 'vuetify/styles'

export const vuetify = createVuetify({
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light', // 'system' | 'light' | 'dark'
    themes: {
      light: {
        colors: {
          background: '#f5f5f5',
          surface: '#ffffff',
          primary:'#0176ff', 
          'on-surface': '#1e293b',
          'on-background': '#1e293b',
          'add' : '#10b767'
        }
      }
    }
  },
   locale: {
    locale: 'ru',
    fallback: 'ru',
    messages: { ru },
  },
  defaults:{
    VCard:{
      rounded:'xl',
    },
    VTextField:{
      density:"comfortable",
      variant:'outlined',
      rounded:'lg'
    },
    VTextarea:{
      density:"comfortable",
      variant:'outlined',
      rounded:'lg'
    },
    VNumberInput:{
      density:"comfortable",
      variant:'outlined',
      rounded:'lg',

    },
    VAutocomplete:{
      density:"comfortable",
      variant:'outlined',
      rounded:'lg'
    },
  }
})