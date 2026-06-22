<script setup lang="ts">
import BaseDialog from '@components/BaseComponents/BaseDialog/BaseDialog.vue';
import ForeignNationalEnrollments from './ForeignNationalEnrollments.vue';
import ForeignNationalActionsDropdown from './ForeignNationalActionsDropdown.vue';
import { computed, onMounted, ref } from 'vue';
import { useHttp } from '@inertiajs/vue3'
import { DateFormatter } from '@helpers/DateFormatter';
import countries from '@data/countries.json'
import { ForeignNational } from '@/interfaces/ForeignNational';
import ForeignNationalsDocuments from './ForeignNationalsDocuments.vue';

const props = defineProps<{
    foreignNationalId:number
}>()

const http = useHttp<{}, {foreignNational:ForeignNational}>()

const isOpen = defineModel<boolean>({default:false})

const foreignNational = ref<ForeignNational | null>(null)

const getForeignNational = async () => {
    error.value = false
    http.get(`/foreign-nationals/${props.foreignNationalId}`,{
        onSuccess:(response)=>{
            foreignNational.value = response.foreignNational
        },
        onFinish() {
            if(! http.wasSuccessful){
                error.value = true
            }
        },
    })
}

onMounted(async() => {
    if(!props.foreignNationalId) return
    getForeignNational()
})

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
const error = ref(false)
</script>

<template>
    
    <BaseDialog 
        max-width="800"
        :title="`Иностранный гражданин (ID ${foreignNational?.id ?? ''})`"
        :loading="http.processing"
        v-model="isOpen"
        :error="error"
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
                v-if="dropDownAccess"
            />
        </template>

        <v-card-text class="pb-2">
    <div class="text-h6 font-weight-medium">
        {{ foreignNational?.fullName }}
    </div>

    <div class="text-subtitle-2 text-medium-emphasis">
        {{ foreignNational?.fullNameLatin }}
    </div>

    <div class="text-caption text-medium-emphasis mt-1">
        {{ new DateFormatter(foreignNational?.dateBirth ?? '').format('d.m.Y') }}
        · {{ getCountryTitle(foreignNational?.citizenship ?? null) }}
    </div>
</v-card-text>

<v-divider />

<v-card-text>
    <div class="info-grid">
        <div class="info-row">
            <div class="label">Паспорт</div>
            <div class="value">
                {{ foreignNational?.fullPassport }},
                выдан {{ new DateFormatter(foreignNational?.issuedDate ?? '').format('d.m.Y') }}
                ({{ foreignNational?.issuedBy }})
            </div>
        </div>

        <div class="info-row">
            <div class="label">Номер телефона</div>
            <div class="value">
                {{ formatPhoneNumber(foreignNational?.phone ?? '') }}
            </div>
        </div>

        <div class="info-row">
            <div class="label">Ответственный</div>
            <div class="value">
                {{ foreignNational?.creatorFullName ?? '' }}
            </div>
        </div>
    </div>
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

<style lang="css" scoped>
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.info-row {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 12px;
    align-items: start;

    padding: 10px 0;
}

.label {
    font-size: 12px;
    color: rgba(var(--v-theme-on-surface), 0.6);
}

.value {
    font-size: 14px;
    font-weight: 500;
    line-height: 1.4;
    color: rgba(var(--v-theme-on-surface), 0.9);
}
</style>