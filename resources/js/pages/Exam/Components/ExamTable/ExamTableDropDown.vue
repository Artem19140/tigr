<script setup lang="ts">
import BaseThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@composables/useModals';
import { ExamPagePermissions } from '@/interfaces/Exam';

const props = defineProps<{
    permissions:ExamPagePermissions
}>()

const modals = useModals()
</script>

<template>
    <BaseThreeDotDropdown v-if="permissions.flatTable || permissions.frdo">
        <v-list-item 
            v-if="permissions.frdo"
            title="ФИС ФРДО" 
            @click="modals.open('frdo')" 
        />
        <v-list-item 
            title="Плоская таблица" 
            v-if="permissions.flatTable"
            @click="modals.open('flatTable')"
        />
    </BaseThreeDotDropdown>
</template>