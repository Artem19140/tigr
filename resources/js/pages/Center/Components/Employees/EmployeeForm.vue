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
    <v-text-field 
        label="Фамилия"
        v-model="form.surname"
        :error-messages="errors.surname"
        :readonly="readOnly"
    />
    <v-text-field 
        label="Имя"
        v-model="form.name"
        :error-messages="errors.name"
        :readonly="readOnly"
    />
    <v-text-field 
        label="Отчество"
        v-model="form.patronymic"
        :error-messages="errors.patronymic"
        :readonly="readOnly"
    />

    <V-autocomplete 
        label="Роли"
        :loading="loading"
        :disabled="loading"
        v-model="form.roles"
        :items="rolesList"
        item-title="label"
        item-value="id"
        multiple
        :error-messages="errors.roles"
        :readonly="readOnly"
    />

    <v-text-field 
        label="e-mail@"
        v-model="form.email"
        :error-messages="errors.email"
        :readonly="readOnly"
    />
</template>