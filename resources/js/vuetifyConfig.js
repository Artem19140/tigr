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
    }
  }
})