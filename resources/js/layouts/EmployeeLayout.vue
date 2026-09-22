<script setup lang="ts">
import { computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useConfirm } from '@/composables/useConfirm';
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import BaseLayout from './BaseLayout.vue';
import { mdiPaw, mdiAccountGroup, mdiFileChartOutline, 
  mdiOfficeBuilding, mdiCog, mdiLogout, mdiClipboardText, mdiFileSign } from '@mdi/js'
import LogoutAllDevicesModal from './LogoutAllDevicesModal.vue';

const page = usePage<any>()
const navigation =  computed(() => page.props?.auth?.navigation)

const logout = async () => {
  const {confirmOpen} = useConfirm()
  const ok = await confirmOpen('Выйти из аккаунта?')
  if(!ok) return 
  router.post(navigation.value.auth.logout.url)
}
const user = page.props?.auth?.user ?? null

const employeeName = `${user?.surname} ${user?.name}`
const activeItem = ref(page.url ?? '')

const menuProps = {
  foreignNationals: {
	icon: mdiAccountGroup,
	label: 'Иностранные граждане'
  },
  exams: {
	icon: mdiClipboardText,
	label: 'Экзамены'
  },
  myExams: {
	icon:mdiFileSign,
	label:'Мои экзамены'
  },
  reports: {
	icon: mdiFileChartOutline,
	label: 'Отчеты'
  },
  center: {
	icon: mdiOfficeBuilding,
	label: 'Центр'
  },
  admin: {
	icon: mdiCog, 
	label: 'Админ панель'
  },
} 
type MenuKey = keyof typeof menuProps
const logoutAll = ref<boolean>(false)
</script>

<template>
    <BaseLayout>
        <v-navigation-drawer
            permanent
            rail
            expand-on-hover
            class="border-r border-gray-200"
        >
            <div class="flex h-full flex-col">
                <!-- User -->
                <div class="px-2 py-3">
                    <v-list-item
                        :title="employeeName"
                        :subtitle="user?.job_title"
                        :prepend-icon="mdiPaw"
                        class="rounded-lg"
                    />
                </div>

                <div class="px-3">
                    <div class="border-t border-gray-200" />
                </div>

                <v-list
                    v-model="activeItem"
                    density="comfortable"
                    nav
                    class="px-2 pt-3"
                >
                    <v-list-item
                        v-for="(item, key) in navigation.menu"
                        :key="key"
                        :value="item.url"
                        :title="menuProps[key].label"
                        :prepend-icon="menuProps[key].icon"
                        class="mb-1 rounded-lg"
                        color="primary"
                        @click="router.visit(item.url)"
                    />
                </v-list>

                <!-- Logout -->
                <div class="mt-auto px-2 pb-3">
                    <v-list
                        density="comfortable"
                        nav
                        class="pa-0"
                    >
                        <v-list-item
                            title="Выйти из аккаунта"
                            :prepend-icon="mdiLogout"
                            class="rounded-lg"
                            @click="logout"
                        >
                            <template #append>
                                <BaseThreeDotDropdown nav>
                                    <v-list-item
                                        title="Выйти с других устройств"
                                        @click="logoutAll = true"
                                    />
                                </BaseThreeDotDropdown>
                            </template>
                        </v-list-item>
                    </v-list>
                </div>
            </div>
        </v-navigation-drawer>

        <slot />
    </BaseLayout>

    <LogoutAllDevicesModal
        v-model="logoutAll"
        :url="navigation.auth.logoutAll.url"
    />
</template>