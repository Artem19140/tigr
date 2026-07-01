<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3';
import AppAutocomplete from '@components/UI/AppAutocomplete/AppAutocomplete.vue';
import AppInput from '@components/UI/AppInput/AppInput.vue';
import AppTextarea from '@components/UI/AppTextarea/AppTextarea.vue';
import AppNumberInput from '@components/UI/AppNumberInput/AppNumberInput.vue';
import { Address } from '@/interfaces/Address';
import { Employee } from '@/interfaces/Employee';
import { ExamType } from '@/interfaces/Exam';

const props = defineProps<{
    form:any, 
    hasEnrollment?:boolean
}>()

const addresses = ref<Address[]>()
const examiners = ref<Employee[]>()
const examTypes = ref<ExamType[]>()


const http = useHttp()

onMounted( async () => {
    http.get('/exams/create/data', {
        onSuccess:(response:any) => {
            addresses.value = response.addresses
            examiners.value = response.examiners
            examTypes.value = response.examTypes
        }
    })
})
function required (v:any) {
    return !!v || 'Поле обязательно'
}
</script>

<template>
    <v-row>
        <v-col cols="12">
            <AppAutocomplete
                label="Тип экзамена"
                :rules="[required]"
                item-title="name"
                item-value="id"
                :items="examTypes"
                v-model="form.examTypeId"
                :error-messages="form.errors.examTypeId"
                :loading="http.processing"
                :disabled="hasEnrollment"
                clearable
                prepend-inner-icon="mdi-school-outline"
            />
        </v-col>

        <v-col
            cols="12"
            md="4"
        >
            <AppInput
                type="date"
                label="Дата"
                :rules="[required]"
                v-model="form.date"
                :disabled="hasEnrollment"
                :error-messages="form.errors.date"
                prepend-inner-icon="mdi-calendar-outline"
            />
        </v-col>

        <v-col
            cols="12"
            md="4"
        >
            <AppInput
                type="time"
                label="Время"
                :rules="[required]"
                v-model="form.time"
                :disabled="hasEnrollment"
                :error-messages="form.errors.time"
                prepend-inner-icon="mdi-clock-outline"    
            />
        </v-col>

        <v-col
            cols="12"
            md="4"
        >
            <AppNumberInput
                label="Вместимость"
                v-model="form.capacity"
                :rules="[required]"
                :min="0"
                :error-messages="form.errors.capacity"
                prepend-inner-icon="mdi-account-group-outline"
                control-variant="hidden"
            />
        </v-col>

        <v-col cols="12">
            <AppAutocomplete
                label="Адрес"
                item-title="address"
                item-value="id"
                :items="addresses"
                :rules="[required]"
                v-model="form.addressId"
                :disabled="hasEnrollment"
                :loading="http.processing"
                :error-messages="form.errors.addressId"
                clearable
                prepend-inner-icon="mdi-map-marker-outline"
            />
        </v-col>

        <v-col cols="12">
            <AppAutocomplete
                label="Экзаменаторы"
                item-title="fullName"
                item-value="id"
                :items="examiners"
                :rules="[required]"
                v-model="form.examiners"
                :loading="http.processing"
                :error-messages="form.errors.examiners"
                multiple
                chips
                closable-chips
                clearable
                prepend-inner-icon="mdi-account-tie-outline"
            />
        </v-col>

        <v-col cols="12">
            <v-divider class="my-2" />
        </v-col>

        <v-col cols="12">
            <div class="text-subtitle-2 text-medium-emphasis mb-2">
                Дополнительно
            </div>

            <AppTextarea
                label="Комментарий"
                v-model="form.comment"
                :error-messages="form.errors.comment"
                hint="Максимум 256 символов"
                maxlength="256"
                counter

                auto-grow
                prepend-inner-icon="mdi-text-box-outline"
            />
        </v-col>
    </v-row>
</template>