<script setup lang="ts">
import ThreeDotDropdown from '@components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import { useModals } from '@composables/useModals';
import { ForeignNational } from '@/interfaces/ForeignNational';
import BaseListItem from '@/components/BaseComponents/BaseList/BaseListItem.vue';

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
        <BaseListItem 
            title="Записать на экзамен"
            v-if="foreignNational?.permissions.enroll"
            @click="open('enrollment', {foreignNational})"
        />
        <BaseListItem
            title="Редактировать"
            v-if="foreignNational?.permissions.edit"
            @click="open('foreignNationalEdit', {
                foreignNational
            })"
        />
    </ThreeDotDropdown>
</template>