<script setup lang="ts">

import { Roles } from '@/constants/Roles';
import { computed, onMounted, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import { EmployeeFormI } from '@/interfaces/Employee';

const props = defineProps<{
    errors: Partial<Record<keyof EmployeeFormI, string>>,
    loading:boolean
}>()

const form = defineModel<EmployeeFormI>('form', {required:true})
const readOnly = computed(() => props.loading)

const rolesList = ref<Roles[]>()

const http = useHttp()

onMounted(() => {
    http.get('/roles', {
        onSuccess:(response : any) => {
            rolesList.value = response.data
        }
    })
})
</script>

<template>
    <div class="space-y-5">
        <!-- ФИО -->
        <div>
            <div class="mb-4 text-sm font-semibold text-gray-700">
                Личные данные
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <v-text-field
                    v-model="form.surname"
                    label="Фамилия"
                    :error-messages="errors.surname"
                    :readonly="readOnly"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                />

                <v-text-field
                    v-model="form.name"
                    label="Имя"
                    :error-messages="errors.name"
                    :readonly="readOnly"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                />

                <v-text-field
                    v-model="form.patronymic"
                    label="Отчество"
                    :error-messages="errors.patronymic"
                    :readonly="readOnly"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                />
            </div>
        </div>

        <!-- Доступ -->
        <div>
            <div class="mb-4 text-sm font-semibold text-gray-700">
                Доступ
            </div>

            <div class="space-y-5">
                <v-autocomplete
                    v-model="form.roles"
                    label="Роли"
                    :items="rolesList"
                    item-title="label"
                    item-value="id"
                    :loading="loading"
                    :disabled="loading"
                    :readonly="readOnly"
                    :error-messages="errors.roles"
                    multiple
                    chips
                    closable-chips
                    clearable
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                />

                <v-text-field
                    v-model="form.email"
                    label="E-mail"
                    type="email"
                    :error-messages="errors.email"
                    :readonly="readOnly"
                    variant="outlined"
                    density="comfortable"
                    hide-details="auto"
                />
            </div>
        </div>
    </div>
</template>