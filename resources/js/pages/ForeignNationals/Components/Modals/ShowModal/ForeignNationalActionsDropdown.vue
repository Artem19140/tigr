<script setup lang="ts">
import ThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@composables/useModals';
import { ForeignNational } from '@/interfaces/ForeignNational';

const {open} = useModals()

const props = defineProps<{
    foreignNational:ForeignNational | null
}>()

const emit = defineEmits<{
    (e:'edit', value: ForeignNational):void
}>()

</script>

<template>
    <ThreeDotDropdown>
        <v-list-item  
            title="Записать на экзамен"
            v-if="foreignNational?.permissions.enroll"
            @click="open('enrollment', {foreignNational})"
        />
        <v-list-item 
            title="Редактировать"
            v-if="foreignNational?.permissions.edit"
            @click="open('foreignNationalEdit', {
                foreignNational
            })"
        />
    </ThreeDotDropdown>
</template>