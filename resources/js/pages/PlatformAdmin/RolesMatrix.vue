
<script setup lang="ts">
import { computed, ref } from 'vue'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue'
import { mdiCheckCircle, mdiMagnify, mdiMinusCircleOutline } from '@mdi/js';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: [EmployeeLayout],
})

interface Permission {
    value: string
    label: string
    roles: string[]
}

const props = defineProps<{
    permissions: Permission[]
    roles: string[]
}>()

const search = ref('')

const headers = computed(() => [
    {
        title: 'Разрешение',
        key: 'label',
        minWidth: '280px',
        fixed: true,
    },
    ...props.roles.map(role => ({
        title: role,
        key: role,
        align: 'center' as const,
        width: '150px',
        sortable: false,
    })),
])

const items = computed(() =>
    props.permissions
        .filter(permission =>
            permission.label.toLowerCase().includes(search.value.toLowerCase()) ||
            permission.value.toLowerCase().includes(search.value.toLowerCase())
        )
        .map(permission => ({
            ...permission,
            ...Object.fromEntries(
                props.roles.map(role => [
                    role,
                    permission.roles.includes(role),
                ])
            ),
        }))
)
</script>

<template>
    <Head title="Матрица ролей"></Head>
    <v-container>
        <div class="d-flex align-center justify-space-between mb-6">
            <div>
                <h1 class="text-h5 font-weight-bold">
                    Матрица ролей
                </h1>
                <div class="text-medium-emphasis text-body-2 mt-1">
                    Сравнение разрешений пользователей по ролям
                </div>
            </div>
        </div>

        <v-card flat>
            <v-card-text>
                <v-text-field
                    v-model="search"
                    label="Поиск разрешения"
                    :prepend-inner-icon="mdiMagnify"
                    variant="outlined"
                    density="compact"
                    hide-details
                    clearable
                    max-width="400"
                />
            </v-card-text>

            <v-divider />

            <v-data-table
                :headers="headers"
                :items="items"
                item-value="value"
                hover
                :items-per-page="-1"
                hide-default-footer
            >
                <template #item.label="{ item }">
                    <div>
                        <div class="font-weight-medium">
                            {{ item.label }}
                        </div>
                        <div class="text-caption text-medium-emphasis">
                            {{ item.value }}
                        </div>
                    </div>
                </template>

                <template
                    v-for="role in roles"
                    #[`item.${role}`]="{ item }"
                    :key="role"
                >
                    <div class="d-flex justify-center">
                        <v-icon
                            v-if="item[role]"
                            :icon="mdiCheckCircle"
                            color="success"
                            size="20"
                        />
                        <v-icon
                            v-else
                            :icon="mdiMinusCircleOutline"
                            color="disabled"
                            size="20"
                        />
                    </div>
                </template>

                <template #no-data>
                    <div class="text-center pa-6 text-medium-emphasis">
                        Разрешения не найдены
                    </div>
                </template>
            </v-data-table>

            <v-divider />

            <v-card-text class="d-flex align-center ga-6">
                <div class="d-flex align-center ga-2">
                    <v-icon color="success" :icon="mdiCheckCircle" />
                    <span class="text-body-2">Разрешено</span>
                </div>

                <div class="d-flex align-center ga-2">
                    <v-icon color="disabled" :icon="mdiMinusCircleOutline" />
                    <span class="text-body-2">Не разрешено</span>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>
