<script setup lang="ts">
import { computed } from 'vue';
import countries from '@data/countries.json'
import AppOptionalInput from '@/components/UI/AppOptionalInput/AppOptionalInput.vue';
import { ForeignNationalEditForm, ForeignNationalFormI } from '@/interfaces/ForeignNational';
import AppTooltip from '@/components/UI/AppTooltip/AppTooltip.vue';

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
    <v-card 
        class="mb-4"
        rounded="xl"
    >
        <v-card-text>
            <v-row class="mb-2">
                <v-col cols="12">
                    <div class="text-sm font-medium text-medium-emphasis">
                        Нотариальный перевод 
                    </div>
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field
                        label="Фамилия на кириллице"
                        :rules="[required]"
                        v-model="form.surname"
                        :readonly="readonly"
                        :error-messages="errors.surname"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field 
                    :rules="[required]"
                    label="Имя на кириллице"
                    v-model="form.name"
                    :readonly="readonly"
                    :error-messages="errors.name"
            />
                </v-col>

                <v-col cols="12" md="6">
                    <AppOptionalInput
                        :form="form"
                        v-model:input="form.patronymic"
                        v-model:checkbox="form.noPatronymic"
                        :input-attr="{label:'Отчество на кириллице', 'error-messages':errors.patronymic}"
                        :checkbox-attr="{label:'Нет отчества на кириллице', 'error-messages':errors.noPatronymic}"
                    />
                </v-col>



                <v-col cols="12">
                    <div class="text-sm font-medium text-medium-emphasis">
                        Паспортные данные
                    </div>
                </v-col>

                
                <v-col cols="12" md="6">
                    <v-text-field  
                        label="Фамилия на латинице"
                        :rules="[required]"
                        v-model="form.surnameLatin"
                        :readonly="readonly"
                        :error-messages="errors.surnameLatin"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field  
                        label="Имя на латинице"
                        :rules="[required]"
                        v-model="form.nameLatin"
                        :readonly="readonly"
                        :error-messages="errors.nameLatin"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <AppOptionalInput
                        :form="form"
                        v-model:input="form.patronymicLatin"
                        v-model:checkbox="form.noPatronymicLatin"
                        :input-attr="{label:'Отчество на латинице', 'error-messages':errors.patronymicLatin}"
                        :checkbox-attr="{label:'Нет отчества латиница', 'error-messages':errors.noPatronymicLatin}"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field 
                        type="date"
                        :readonly="readonly"
                        :rules="[required]"
                        v-model="form.dateBirth"
                        :error-messages="errors.dateBirth"
                        label="Дата рождения"
                    />
                </v-col>

                <v-col md="6" cols="12">
                    <V-autocomplete
                        label="Гражданство"
                        :readonly="readonly"
                        :rules="[required]"
                        item-title="text"
                        :items="countries"
                        item-value="value"
                        v-model="form.citizenship"
                        :error-messages="errors.citizenship"
                        clearable
                    />
                </v-col>

                <v-col md="6" cols="12">
                    <v-radio-group
                        :rules="[required]"
                        v-model="form.gender"
                        :readonly="readonly"
                        inline
                        label="Пол"
                        :error-messages="errors.gender"
                    >
                        <v-radio
                            label="М"
                            value="M"
                        ></v-radio>
                        <v-radio
                            label="Ж"
                            value="F"
                        ></v-radio>
                    </v-radio-group>
                </v-col>

                <v-col cols="12" md="6">
                    <AppOptionalInput
                        :form="form"
                        v-model:input="form.passportSeries"
                        v-model:checkbox="form.noPassportSeries"
                        :input-attr="{label:'Серия паспорта',  'error-messages':errors.passportSeries}"
                        :checkbox-attr="{label:'Нет серии', 'error-messages':errors.noPassportSeries}"
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <AppOptionalInput
                        :form="form"
                        :rules="[required]"
                        v-model:input="form.passportNumber"
                        v-model:checkbox="form.noPassportNumber"
                        :input-attr="{label:'Номер паспорта', 'error-messages':errors.passportNumber}"
                        :checkbox-attr="{label:'Нет номера', 'error-messages':errors.noPassportNumber}"
                    />
                </v-col>

                <v-col cols="12" md="6">  
                    <v-text-field
                        :rules="[required]"
                        label="Кем выдан"
                        :readonly="readonly"
                        v-model="form.issuedBy"
                        :error-messages="errors.issuedBy"
                        clearable
                    />
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field 
                        type="date"
                        label="Дата выдачи"
                        :rules="[required]"
                        :readonly="readonly"
                        v-model="form.issuedDate"
                        :error-messages="errors.issuedDate"
                    />  
                </v-col>



                <v-col cols="12">
                    <div class="text-sm font-medium text-medium-emphasis">
                        Адрес регистрации  
                    </div>
                </v-col>

                <v-col cols="12" md="6" class="mb-4">
                    <v-text-field 
                        label="Адрес"
                        :rules="[required]"
                        :readonly="readonly"
                        v-model="form.addressReg"
                        :error-messages="errors.addressReg"
                    /> 
                </v-col>
                

                <v-col cols="12">
                    <div class="text-sm font-medium text-medium-emphasis">
                        Контакты
                    </div>
                </v-col>

                <v-col cols="12" md="6">
                    <v-text-field 
                        label="Номер телефона"
                        placeholder="0123456789"
                        :readonly="readonly"
                        v-model="form.phone"
                        prefix="+7"
                        :error-messages="errors.phone"
                        maxlength="10"
                        :disabled="form.noPhone"
                    /> 

                    <v-checkbox 
                        label="Нет номера"
                        v-model="form.noPhone"
                        :error-messages="errors.noPhone"
                        @click="() => {
                            if(form.noPhone){
                                form.phone = null
                            }
                        }"
                    />
                </v-col>
            </v-row>
        </v-card-text>
    </v-card>

    <v-card class="mb-4" rounded="xl" variant="flat" >
        <v-card-title>
            Дополнительная информация
            <AppTooltip
                text="например, лицо с ограниченными возможностями здоровья"
            />
        </v-card-title>
        <v-card-text>
            <v-container fluid>
                <v-row>
                    <v-textarea
                        label="Введите комментарий"
                        auto-grow
                        rows="1"
                        v-model="form.comment"
                        :error-messages="errors.comment"
                    />
                </v-row>
            </v-container>
        </v-card-text>
    </v-card>
</template>