<script setup lang="ts">
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import ForeignNationalEnrollments from './ForeignNationalEnrollments.vue';
import ForeignNationalActionsDropdown from './ForeignNationalActionsDropdown.vue';
import { computed, onMounted, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3'
import { DateFormatter } from '@helpers/DateFormatter';
import countries from '@data/countries.json'
import { ForeignNational, ForeignNationalActionsPermissions } from '@/interfaces/ForeignNational';
import ForeignNationalsDocuments from './ForeignNationalsDocuments.vue';

const props = defineProps<{
    foreignNationalId?:number
}>()

const http = useHttp<{}, {foreignNational:ForeignNational, permissions:ForeignNationalActionsPermissions}>()

const isOpen = defineModel<boolean>({default:false})
const foreignNational = ref<ForeignNational | null>(null)

const getForeignNational = async () => {
    http.get(`/foreign-nationals/${props.foreignNationalId}`,{
        onSuccess:(response)=>{
            foreignNational.value = response.foreignNational
        }
    })
}

onMounted(async() => {
    if(!props.foreignNationalId) return
    getForeignNational()
})

const edit = (value:ForeignNational) => {
    foreignNational.value = value
}

const getCountryTitle = (value:string | null) => {
    const result = countries.find(item => item.value === value);
    return result ? result.text : '-';
}

const dropDownAccess = computed(() =>{
    if(! foreignNational.value){
        return false
    }
    return foreignNational.value.permissions.edit || foreignNational.value.permissions.enroll
})

function formatPhoneNumber(cleaned: string ) {
  if (!cleaned || cleaned.length !== 10 || !/^\d+$/.test(cleaned)) {
    return "+7 (___) ___-__-__"; 
  }
  return (
    "+7 (" +
    cleaned.substring(0, 3) + ") " +
    cleaned.substring(3, 6) + "-" +
    cleaned.substring(6, 8) + "-" +
    cleaned.substring(8, 10)
  );
}
</script>

<template>
    
    <BaseDialog 
        width="700"
        height="900"
        :title="`Карточка ИГ (ID ${foreignNational?.id ?? ''})`"
        :loading="http.processing"
        v-model="isOpen"
        :error="!http.wasSuccessful"
        :onRetry="getForeignNational"
        @before-close="(close) => {
            http.cancel()
            close()
        }"
        skeleton="paragraph,divider, paragraph, divider, list-item-two-line, divider"
    >
    
        <template #titleActions>
            <ForeignNationalActionsDropdown 
                :foreignNational="foreignNational"
                @edit="edit"
                v-if="dropDownAccess"
            />
        </template>

        <v-card-text class="ml-4">
            <div class="text-headline-small">{{foreignNational?.fullName }}</div>
            <div class="text-subtitle-1">{{foreignNational?.fullNameLatin}}</div>
            <div class="text-subtitle-2">
                {{new DateFormatter(foreignNational?.dateBirth ?? '').format('d.m.Y')}} 
                ({{getCountryTitle(foreignNational?.citizenship ?? null) }})
            </div> 
        </v-card-text>

        <v-divider></v-divider>

        <v-card-text>
            <v-list>
                <v-list-item>
                    <v-list-item-subtitle>Паспорт</v-list-item-subtitle>
                    <v-list-item-title>
                        {{`${foreignNational?.fullPassport ?? ''}, 
                        выдан ${new DateFormatter(foreignNational?.issuedDate ?? '').format('d.m.Y')}
                        (${foreignNational?.issuedBy ?? ''})`}}
                    </v-list-item-title>
                </v-list-item>
                <v-list-item>
                    <v-list-item-subtitle>Номер телефона</v-list-item-subtitle>
                    <v-list-item-title>{{formatPhoneNumber(foreignNational?.phone ?? '')}}</v-list-item-title>
                </v-list-item>
                <v-list-item>
                    <v-list-item-subtitle>Ответственный</v-list-item-subtitle>
                    <v-list-item-title>{{foreignNational?.creatorFullName ?? ''}}</v-list-item-title>
                </v-list-item>
            </v-list>
        </v-card-text>
        
        <div
            v-if="foreignNational?.permissions.documents"
        >
            <v-divider />
            <v-card-text class="ml-4">
                <ForeignNationalsDocuments
                    :documents="foreignNational?.documents"
                />
            </v-card-text>
        </div>

        <v-divider />

        <v-card-text>
            <ForeignNationalEnrollments 
                v-if="foreignNational" 
                :enrollments="foreignNational?.enrollments"
            />
        </v-card-text>
    </BaseDialog>
</template>