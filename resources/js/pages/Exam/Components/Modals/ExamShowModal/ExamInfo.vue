<script setup lang="ts">
import { Exam } from '@/interfaces/Exam';
import { computed } from 'vue';

const props = defineProps<{
    exam:Exam | null
}>()

const examiners = computed(() =>{
    return props.exam?.examiners.map(s => s.fullName).join(', ');
})
</script>

<template>
    <v-list class="pa-0">

    <v-list-item
        v-if="exam?.cancelledAt || exam?.cancelledReason"
        class="text-error"
    >
        <template #prepend>
            <v-icon color="error">mdi-alert-circle-outline</v-icon>
        </template>

        <div>
            <div class="text-caption text-medium-emphasis">
                Причина отмены
            </div>

            <div>
                {{ exam?.cancelledReason ?? '-' }}
            </div>
        </div>
    </v-list-item>

    <v-list-item>
        <template #prepend>
            <v-icon>mdi-pound</v-icon>
        </template>

        <div>
            <div class="text-caption text-medium-emphasis">
                Сессия / группа
            </div>

            <div>
                {{ exam?.sessionNumber ?? '-' }} / {{ exam?.group ?? '-' }}
            </div>
        </div>
    </v-list-item>

    <!-- <v-list-item>
        <template #prepend>
            <v-icon>mdi-calendar-clock-outline</v-icon>
        </template>

        <div>
            <div class="text-caption text-medium-emphasis">
                Дата
            </div>

            <div>
                {{ new DateFormatter(exam?.beginTime ?? '').format('H:i, d.m.Y') }}
            </div>
        </div>
    </v-list-item> -->

    <v-list-item>
        <template #prepend>
            <v-icon>mdi-map-marker-outline</v-icon>
        </template>

        <div>
            <div class="text-caption text-medium-emphasis">
                Адрес
            </div>

            <div style="white-space: normal">
                {{ exam?.address ?? '-' }}
            </div>
        </div>
    </v-list-item>

    <v-list-item>
        <template #prepend>
            <v-icon>mdi-account-group-outline</v-icon>
        </template>

        <div>
            <div class="text-caption text-medium-emphasis">
                Экзаменаторы
            </div>

            <div style="white-space: normal">
                {{ examiners }}
            </div>
        </div>
    </v-list-item>

    <v-list-item v-if="exam?.comment">
        <template #prepend>
            <v-icon>mdi-message-text-outline</v-icon>
        </template>

        <div>
            <div class="text-caption text-medium-emphasis">
                Комментарий
            </div>

            <div style="white-space: normal">
                {{ exam?.comment }}
            </div>
        </div>
    </v-list-item>

</v-list>
</template>