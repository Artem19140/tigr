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
        max-width="700"
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
        <template #header>
            <div class="flex flex-column">
                <div class="text-h6 font-weight-medium">
                    {{ foreignNational?.fullName }}
                </div>

                <div class="text-caption text-medium-emphasis">
                    ID {{ foreignNational?.id }}
                    · {{ getCountryTitle(foreignNational?.citizenship ?? null) }}
                </div>
            </div>
        </template>
    
        <template #titleActions>
            <ForeignNationalActionsDropdown 
                :foreignNational="foreignNational"
                v-if="dropDownAccess"
            />
        </template>

        <v-card-text class="pt-4">
            <div class="info-grid">
                <div class="info-row">
                    <div class="label">ФИО (лат.)</div>
                    <div class="value text-subtitle-1">
                        {{ foreignNational?.fullNameLatin }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="label">Дата рождения</div>
                    <div class="value">
                        {{ new DateFormatter(foreignNational?.dateBirth ?? '').format('d.m.Y') }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="label">Паспорт</div>
                    <div class="value">
                        {{ foreignNational?.fullPassport }} ·
                        {{ foreignNational?.issuedBy }} ·
                        {{ new DateFormatter(foreignNational?.issuedDate ?? '').format('d.m.Y') }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="label">Телефон</div>
                    <div class="value">
                        {{ formatPhoneNumber(foreignNational?.phone ?? '') }}
                    </div>
                </div>

                <div class="info-row">
                    <div class="label">Ответственный</div>
                    <div class="value">
                        {{ foreignNational?.creatorFullName }}
                    </div>
                </div>
            </div>
        </v-card-text>
        
        <div
            v-if="foreignNational?.permissions.documents"
        >
            <v-divider />
            <v-card-text>
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