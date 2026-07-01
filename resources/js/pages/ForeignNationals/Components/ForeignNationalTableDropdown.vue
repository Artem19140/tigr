<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@composables/useModals';
import { ForeignNationalPagePermissions } from '@/interfaces/ForeignNational';

const props = defineProps<{
    permissions:ForeignNationalPagePermissions
}>()

const modals = useModals()
</script>

<template>
    <BaseThreeDotDropdown>
        <v-list-item  
            title="Еженедельный отчет"
            v-if="permissions.ministryEducation"
            @click="modals.open('ministryEducationReport')"
        />
        <v-list-item  
            title="Экспорт ИГ"
            v-if="permissions.export"
            @click="modals.open('foreignNationalExport')"
        />
        <v-list-item  
            title="Статистика"
            v-if="permissions.statistics"
            @click="modals.open('statistics')"
        />
    </BaseThreeDotDropdown>
</template>