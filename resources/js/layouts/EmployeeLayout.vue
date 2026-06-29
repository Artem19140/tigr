<script setup lang="ts">
import { computed, ref } from 'vue'
import { useAuth } from '@composables/useAuth';
import { router, usePage } from '@inertiajs/vue3'
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@/composables/useModals';
import BaseLayout from './BaseLayout.vue';

const page = usePage<any>()
const can =  computed(() => page.props?.auth?.can)

const go = (url:string) => {
  router.visit(url)
}

const logout = async () => {
  const {confirmOpen} = useConfirmDialog()
  const ok = await confirmOpen('Выйти из аккаунта?')
  if(!ok) return 
  router.post('/logout')
}
const {user} = useAuth()

const centerId = user?.center_id
const employeeName = `${user?.surname} ${user?.name}`
const activeItem = ref('')

const {open} = useModals()

const menu: Array<MenuElem> = [
  {
    title:"Иностранные граждане" ,
    prependIcon:"mdi-account-group",
    url:'/foreign-nationals',
    value:"foreignNationals",
    allowed:can.value.foreignNationals
  },
  {
    title:"Экзамены" ,
    prependIcon:"mdi-school" ,
    url:'/exams',
    value:"exams",
    allowed:can.value.exams
  },
  {
    title:"Мониторинг" ,
    prependIcon:"mdi-monitor-eye" ,
    url:'/exams/monitoring',
    value:"monitoring",
    allowed:can.value.monitoring
  },
  {
    title:"Проверка" ,
    prependIcon:"mdi-clipboard-check" ,
    url:'/exams/checking',
    value:"checking",
    allowed:can.value.checking
  },
  {
    title:"Расписание" ,
    prependIcon:"mdi-calendar-month" ,
    url:'/exams/schedule',
    value:"schedule",
    allowed:can.value.schedule
  },
  {
    title:"Центр" ,
    prependIcon:"mdi-office-building" ,
    url:`/centers/${centerId}`,
    value:"center",
    allowed:can.value.center
  },
  {
    title:"Панель админа" ,
    prependIcon:"mdi-cog" ,
    url:'/admin/home',
    value:"admin",
    allowed:can.value.adminPanel
  }
]

interface MenuElem{
  title:string,
  prependIcon:string,
  url:string,
  value:string,
  allowed:boolean
}

const visibleItems = computed(() =>
  menu.filter(item => item.allowed)
)
</script>

<template>
  <BaseLayout>
    <v-navigation-drawer 
      expand-on-hover
      permanent
      rail
      color=""
    >
      <div class="d-flex flex-column fill-height">
        <v-list>
          <v-list-item
            :subtitle="user?.job_title"
            :title="employeeName"
            prepend-icon="mdi-paw" 
          >
          </v-list-item>
          
        </v-list>

        <v-divider></v-divider>

        <v-list density="compact" nav v-model="activeItem">
          <v-list-item
            v-for="(item, index) in visibleItems"
            :key="index"
            :title="item.title"
            :prepend-icon="item.prependIcon"
            @click="() => go(item.url)"
            :value="item.value"
          />
        </v-list>
    
        <v-list density="compact" nav class="mt-auto">
          <v-list-item
            prepend-icon="mdi-logout" 
            title="Выйти из аккаунта" 
            @click="logout"
          >
            <template #append>
              <BaseThreeDotDropdown nav>
                <v-list-item
                  title="Выйти с других устройств"
                  @click="open('logoutAll')"
                />
              </BaseThreeDotDropdown>
            </template>
          </v-list-item>
        </v-list>
      </div>
    </v-navigation-drawer >
    <slot />
  </BaseLayout>
</template>



<style>
html {
  overflow-y: scroll;
}
</style>