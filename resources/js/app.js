import '../css/app.css'
import './bootstrap.js'
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { useHttpErrorHandler } from '@/composables/useHttpErrorHandler';
import { http, router } from '@inertiajs/vue3';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { vuetify } from '@/vuetifyConfig'

http.onError((error) => {
  const response = (error).response
  if (!response) return
  useHttpErrorHandler().handle(response)
})

const { add } = useSnackbarQueue()

router.on('flash', (event) => {
  if(event.detail.flash.success){
    add(String(event.detail.flash.success), 'green')
  }

  if(event.detail.flash.error){
    add(String(event.detail.flash.error), 'red')
  }
})

createInertiaApp({
  
  resolve: name => {
    const pages = import.meta.glob('./pages/**/*.vue')
    return pages[`./pages/${name}.vue`]()
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(vuetify)
      .mount(el)
  },
  layout: () => BaseLayout,
  defaults: {
    future: {
      useDialogForErrorModal: true,
    },
    visitOptions: (href, options) => {
      return { viewTransition: true };
    },
  }
})

