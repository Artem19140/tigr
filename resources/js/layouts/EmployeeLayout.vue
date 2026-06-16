<script setup lang="ts">
import { computed, ref } from 'vue'
import BaseLayout from './BaseLayout.vue';
import { useAuth } from '@composables/useAuth';
import { router, usePage } from '@inertiajs/vue3'
import { useConfirmDialog } from '@/composables/useConfirmDialog';
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@/composables/useModals';

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

const menu = [
  
]
</script>

<template>
  <base-layout>
    <template #drawer>
      <v-navigation-drawer 
        expand-on-hover
        permanent
        rail
      >
        <div class="d-flex flex-column fill-height">
          <v-list>
            <v-list-item
              :subtitle="user?.job_title"
              :title="employeeName"
            />
          </v-list>

          <v-divider></v-divider>

          <v-list density="compact" nav v-model="activeItem">
            <v-list-item
              prepend-icon="mdi-account-group" 
              title="Иностранные граждане" 
              v-if="can.foreignNationals"
              @click="go('/foreign-nationals')"  
              value="foreignNationals"
            />

            <v-list-item
              prepend-icon="mdi-school" 
              title="Экзамены" 
              v-if="can.exams"
              @click="go('/exams')"
              value="exams" 
            />

            <v-list-item
              prepend-icon="mdi-monitor-eye" 
              v-if="can.monitoring"
              title="Мониторинг экзамена" 
              value="monitoring" 
              @click="go('/exams/monitoring')"
            />

            <v-list-item
              prepend-icon="mdi-clipboard-check" 
              v-if="can.checking"
              title="Проверка"
              @click="go('/exams/checking')"
              value="checking" 
            />
            
            <v-list-item
              prepend-icon="mdi-calendar-month" 
              title="Расписание" 
              v-if="can.schedule"
              @click="go('/exams/schedule')"
              value="schedule"
            />

            <v-list-item
              prepend-icon="mdi-office-building" 
              v-if="can.center"
              title="Центр" 
              value="center" 
              @click="go(`/centers/${centerId}`)"
            />

            <v-list-item 
              prepend-icon="mdi-cog" 
              v-if="can.adminPanel" 
              title="Панель админа" 
              value="admin" 
              @click="go(`/admin/home`)"
            />
            
          </v-list>
      
          <v-list density="compact" nav class="mt-auto">
            <!-- <v-list-item
              prepend-icon="mdi-book-open-page-variant" 
              title="Инструкция" 
              value="instruction" 
              @click="go('/instruction/foreign-nationals')"
            /> -->
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
    </template>
    <slot />
  </base-layout>
</template>



<style>
html {
  overflow-y: scroll;
}
</style>