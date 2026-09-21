<script setup lang="ts">
import { computed } from 'vue';
import countries from '@data/countries.json'
import AppOptionalInput from '@/components/UI/AppOptionalInput/AppOptionalInput.vue';
import { ForeignNationalEditForm, ForeignNationalFormI } from '@/interfaces/ForeignNational';

const props = defineProps<{
    errors:any,
    loading:boolean
}>()

const form = defineModel<ForeignNationalFormI | ForeignNationalEditForm>('form',{
  required: true
})

const readonly = computed(() => props.loading)

function required (v:any) {
    return !!v || 'Поле обязательно'
}
</script>

<template>
    <div class="space-y-5">

        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-xl font-semibold text-gray-900">
                    Данные иностранного гражданина
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Укажите данные в соответствии с нотариальным переводом и паспортом.
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <div class="mb-4 text-sm font-semibold text-gray-700">
                    Нотариальный перевод
                </div>

                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <v-text-field
                        v-model="form.surname"
                        label="Фамилия на кириллице"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.surname"
                        variant="outlined"
                        density="comfortable"
                    />

                    <v-text-field
                        v-model="form.name"
                        label="Имя на кириллице"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.name"
                        variant="outlined"
                        density="comfortable"
                    />

                    <AppOptionalInput
                        :form="form"
                        v-model:input="form.patronymic"
                        v-model:checkbox="form.noPatronymic"
                        :input-attr="{
                            label: 'Отчество на кириллице',
                            'error-messages': errors.patronymic
                        }"
                        :checkbox-attr="{
                            label: 'Нет отчества на кириллице',
                            'error-messages': errors.noPatronymic
                        }"
                    />
                </div>
            </v-card-text>

            <!-- Паспорт -->
            <v-card-text class="px-6">
                <div class="mb-4 text-sm font-semibold text-gray-700">
                    Паспортные данные
                </div>

                <div class="grid grid-cols-1 items-start gap-5 md:grid-cols-2">
                    <v-text-field
                        v-model="form.surnameLatin"
                        label="Фамилия на латинице"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.surnameLatin"
                        variant="outlined"
                        density="comfortable"
                    />

                    <v-text-field
                        v-model="form.nameLatin"
                        label="Имя на латинице"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.nameLatin"
                        variant="outlined"
                        density="comfortable"
                    />

                    <AppOptionalInput
                        :form="form"
                        v-model:input="form.patronymicLatin"
                        v-model:checkbox="form.noPatronymicLatin"
                        :input-attr="{
                            label: 'Отчество на латинице',
                            'error-messages': errors.patronymicLatin
                        }"
                        :checkbox-attr="{
                            label: 'Нет отчества на латинице',
                            'error-messages': errors.noPatronymicLatin
                        }"
                    />

                    <v-date-input
                        v-model="form.dateBirth"
                        label="Дата рождения"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.dateBirth"
                    />

                    <v-autocomplete
                        v-model="form.citizenship"
                        :items="countries"
                        item-title="text"
                        item-value="value"
                        label="Гражданство"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.citizenship"
                        clearable
                    />

                    <v-radio-group
                        v-model="form.gender"
                        :rules="[required]"
                        :readonly="readonly"
                        label="Пол"
                        inline
                        :error-messages="errors.gender"
                    >
                        <v-radio label="М" value="M" />
                        <v-radio label="Ж" value="F" />
                    </v-radio-group>

                    <AppOptionalInput
                        :form="form"
                        v-model:input="form.passportSeries"
                        v-model:checkbox="form.noPassportSeries"
                        :input-attr="{
                            label: 'Серия паспорта',
                            'error-messages': errors.passportSeries
                        }"
                        :checkbox-attr="{
                            label: 'Нет серии',
                            'error-messages': errors.noPassportSeries
                        }"
                    />

                    <AppOptionalInput
                        :form="form"
                        :rules="[required]"
                        v-model:input="form.passportNumber"
                        v-model:checkbox="form.noPassportNumber"
                        :input-attr="{
                            label: 'Номер паспорта',
                            'error-messages': errors.passportNumber
                        }"
                        :checkbox-attr="{
                            label: 'Нет номера',
                            'error-messages': errors.noPassportNumber
                        }"
                    />

                    <v-text-field
                        v-model="form.issuedBy"
                        label="Кем выдан"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.issuedBy"
                        clearable
                        variant="outlined"
                        density="comfortable"
                    />

                    <v-date-input
                        v-model="form.issuedDate"
                        label="Дата выдачи"
                        :rules="[required]"
                        :readonly="readonly"
                        :error-messages="errors.issuedDate"
                        variant="outlined"
                        density="comfortable"
                    />
                </div>
            </v-card-text>

            <v-card-text class="px-6">
                <div class="mb-4 text-sm font-semibold text-gray-700">
                    Адрес регистрации
                </div>

                <v-text-field
                    v-model="form.addressReg"
                    label="Адрес"
                    :rules="[required]"
                    :readonly="readonly"
                    :error-messages="errors.addressReg"
                    variant="outlined"
                    density="comfortable"
                />
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <div class="mb-4 text-sm font-semibold text-gray-700">
                    Контакты
                </div>

                <div class="max-w-md">
                    <v-text-field
                        v-model="form.phone"
                        label="Номер телефона"
                        placeholder="0123456789"
                        prefix="+7"
                        maxlength="10"
                        :readonly="readonly"
                        :disabled="form.noPhone"
                        :error-messages="errors.phone"
                    />

                    <v-checkbox
                        v-model="form.noPhone"
                        label="Нет номера"
                        :error-messages="errors.noPhone"
                        density="comfortable"
                        hide-details="auto"
                        @click="() => {
                            if (form.noPhone) {
                                form.phone = null
                            }
                        }"
                    />
                </div>
            </v-card-text>
        </v-card>

        <v-card class="overflow-hidden rounded-xl">
            <v-card-text class="px-6 pt-6">
                <div class="text-lg font-semibold text-gray-900">
                    Дополнительная информация
                </div>

                <div class="mt-1 text-sm text-gray-500">
                    Необязательный комментарий.
                </div>
            </v-card-text>

            <v-card-text class="px-6 pb-6">
                <v-textarea
                    v-model="form.comment"
                    label="Комментарий"
                    placeholder="Введите комментарий..."
                    auto-grow
                    rows="3"
                    :error-messages="errors.comment"
                />
            </v-card-text>
        </v-card>
    </div>
</template>