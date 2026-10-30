import { createVuetify } from 'vuetify'
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg'
import { ru } from 'vuetify/locale'
import 'vuetify/styles'
import { StringDateAdapter } from 'vuetify/date/adapters/string'

export const vuetify = createVuetify({
  date: {
    adapter: StringDateAdapter
  },
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light', // 'system' | 'light' | 'dark' primary:'#0176ff', 
    themes: {
      light: {
        colors: {
          background: '#F7F9FC',
          surface: '#FFFFFF',

          primary: '#2563EB',
          secondary: '#64748B',

          success: '#16A36A',
          warning: '#D97706',
          error: '#DC4C4C',
          info: '#0284C7',
          'on-surface': '#1e293b',
          'on-background': '#1e293b',
          'create' : '#10b767'
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
      rounded:'xl'
    },
    VTextField:{
      density:"comfortable",
      variant:'outlined',
      rounded:'lg'
    },
    VTextarea:{
      density:"comfortable",
      variant:'outlined',
      rounded:'lg',
      PersistentHint:true
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
    VDateInput:{
       density:"comfortable",
        variant:'outlined',
        rounded:'lg',
        prependIcon:'',
        prependInnerIcon:"$calendar"
    },
    VBtn:{
      rounded:'lg'
    }
  }
})