
<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useDisplay } from 'vuetify'
import { useConfirm } from '@/composables/useConfirm'
import FileViewModal from '@/components/Modals/FileViewModal.vue'
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue'
import BaseLayout from './BaseLayout.vue'
import LogoutAllDevicesModal from './LogoutAllDevicesModal.vue'

import {
    mdiPaw,
    mdiAccountGroup,
    mdiFileChartOutline,
    mdiOfficeBuilding,
    mdiCog,
    mdiLogout,
    mdiClipboardText,
    mdiFileSign,
    mdiMenu,
} from '@mdi/js'

const page = usePage<any>()
const { mobile } = useDisplay()

const navigation = computed(
    () => page.props?.auth?.navigation
)

const user = computed(
    () => page.props?.auth?.user ?? null
)

const employeeName = computed(
    () => [user.value?.surname, user.value?.name]
        .filter(Boolean)
        .join(' ')
)

const drawer = ref(false)
const logoutAll = ref(false)

const activeItem = ref<string[]>([page.url ?? ''])
const opened = ref<string[]>([])


watch(
    mobile,
    (isMobile) => {
        drawer.value = !isMobile
    },
    { immediate: true }
)

const logout = async () => {
    const { confirmOpen } = useConfirm()
    const ok = await confirmOpen('Выйти из аккаунта?')

    if (!ok) return

    router.post(navigation.value.auth.logout.url)
}

const visit = (url: string) => {
    if (mobile.value) {
        drawer.value = false
    }

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    })
}

const menuProps = {
    foreignNationals: {
        icon: mdiAccountGroup,
    },
    exams: {
        icon: mdiClipboardText,
    },
    myExams: {
        icon: mdiFileSign,
    },
    reports: {
        icon: mdiFileChartOutline,
    },
    center: {
        icon: mdiOfficeBuilding,
    },
    admin: {
        icon: mdiCog,
    },
}

type MenuKey = keyof typeof menuProps
</script>

<template>
    <BaseLayout>
        <v-app-bar
            v-if="mobile"
            density="comfortable"
            flat
            class="border-b border-gray-200"
        >
            <v-app-bar-nav-icon
                :icon="mdiMenu"
                aria-label="Открыть меню"
                @click="drawer = !drawer"
            />

            <v-toolbar-title class="text-base">
                {{ employeeName }}
            </v-toolbar-title>
        </v-app-bar>

        <v-navigation-drawer
            v-model="drawer"
            :permanent="!mobile"
            :temporary="mobile"
            :rail="!mobile"
            :expand-on-hover="!mobile"
            :scrim="mobile"  
        >
            <div class="flex h-full flex-col">
  
                <div class="py-3">
                    <v-list-item
                        :title="employeeName"
                        :prepend-icon="mdiPaw"
                    />
                </div>

                <div class="px-3">
                    <div class="border-t border-gray-200" />
                </div>

             
                <v-list
                    v-model:selected="activeItem"
                    v-model:opened="opened"
                    density="comfortable"
                    nav
                    class="px-2 pt-3"
                >
                    <template
                        v-for="(item, key, index) in navigation.menu"
                        :key="key"
                    >
                        <v-list-group
                            v-if="item.submenu"
                            :value="String(index)"
                        >
                            <template #activator="{ props }">
                                <v-list-item
                                    v-bind="props"
                                    :title="item.label"
                                    :prepend-icon="
                                        menuProps[key as MenuKey]?.icon
                                    "
                                    class="mb-1 rounded-lg"
                                    color="primary"
                                />
                            </template>

                            <v-list-item
                                v-for="subItem in item.submenu"
                                :key="subItem.url"
                                :value="subItem.url"
                                :title="subItem.label"
                                class="mb-1 rounded-lg"
                                color="primary"
                                @click.stop="visit(subItem.url)"
                            />
                        </v-list-group>

                        <v-list-item
                            v-else
                            :value="item.url"
                            :title="item.label"
                            :prepend-icon="
                                menuProps[key as MenuKey]?.icon
                            "
                            class="mb-1 rounded-lg"
                            color="primary"
                            @click="visit(item.url)"
                        />
                    </template>
                </v-list>

              
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
                                        @click.stop="logoutAll = true"
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

    <FileViewModal />
</template>
