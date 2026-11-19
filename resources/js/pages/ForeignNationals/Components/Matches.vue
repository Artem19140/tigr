<script setup lang="ts">
import { debounce } from '@/helpers/debounce';
import { router, useHttp } from '@inertiajs/vue3';
import { mdiAccountOutline, mdiAccountSearchOutline, mdiChevronDown, mdiChevronRight } from '@mdi/js';
import { computed, ref, watch } from 'vue';

const props = defineProps<{
    form: {
        name:string,
        surname:string,
        patronymic:string | undefined,
        passportNumber:string | undefined
        passportSeries:string | undefined
    },
    findMatchesUrl: string
}>()

interface Match{
    id: number,
    fullName: string,
    passport: string,
    showUrl: string
}

const matchesCount = computed(() => matches.value.length)

const matches = ref<Match[]>([])

const matchApi = useHttp<{
    surname: string | undefined
    name: string | undefined
    patronymic: string | undefined
    passportSeries: string | undefined
    passportNumber: string | undefined
}, {matches: Array<Match>}>({
    surname: undefined,
    name: undefined,
    patronymic:  undefined,
    passportSeries:  undefined,
    passportNumber:  undefined
})

watch([
    () => props.form.surname,
    () => props.form.name,
    () => props.form.patronymic,
    () => props.form.passportSeries,
    () => props.form.passportNumber
], () => {
    if(
        ! props.form.surname && ! props.form.name &&
        ! props.form.patronymic && ! props.form.passportSeries &&
        ! props.form.passportNumber 
    ){
        matches.value = []
        return 
    }
    
    matchApi.cancel()
    debounceMatchRequest ()
})

const debounceMatchRequest = debounce(
    () => matchRequest()
, 1000)

const matchRequest = () => {
    matchApi.surname = props.form.surname
    matchApi.name = props.form.name
    matchApi.patronymic = props.form.patronymic
    matchApi.passportSeries = props.form.passportSeries
    matchApi.passportNumber = props.form.passportNumber
    
    matchApi.get(props.findMatchesUrl, {
        onSuccess(response, httpResponse) {
            matches.value = response.matches
        },
    })
}
</script>


<template>
    <div class="d-inline-flex align-center ga-2 text-body-2">
        <v-icon
            :icon="mdiAccountSearchOutline"
            size="18"
            color="medium-emphasis"
        />

        <span class="text-medium-emphasis">
            Совпадения
        </span>

        <template v-if="matchApi.processing">
            <v-progress-circular
                indeterminate
                size="15"
                width="2"
                color="primary"
            />

            <span class="text-medium-emphasis text-caption">
                Поиск...
            </span>
        </template>

        <template v-else>
            <v-chip
                size="x-small"
                density="comfortable"
                :color="matchesCount ? 'warning' : 'success'"
                variant="tonal"
                class="font-weight-bold"
            >
                {{ matchesCount }}
            </v-chip>

            <v-menu
                v-if="matchesCount"
                location="bottom start"
                :close-on-content-click="true"

            >
                <template #activator="{ props }">
                    <v-btn
                        v-bind="props"
                        variant="text"
                        size="x-small"
                        color="primary"
                        :append-icon="mdiChevronDown"
                        class="px-1"
                    >
                        Показать
                    </v-btn>
                </template>

                <v-card
                    elevation="4"
                    
                >
                    <div class="px-4 py-3">
                        <div class="text-subtitle-2">
                            Возможные совпадения
                        </div>

                        <div class="text-caption text-medium-emphasis">
                            Найдено: {{ matchesCount }}
                        </div>
                    </div>

                    <v-divider />

                    <v-list
                        density="compact"
                        class="py-1"
                    >
                        <v-list-item
                            v-for="match in matches"
                            :key="match.id"
                            :title="match.fullName"
                            :subtitle="match.passport"
                            :prepend-icon="mdiAccountOutline"
                            :append-icon="mdiChevronRight"
                            rounded="lg"
                            class="mx-2"
                            @click="router.visit(match.showUrl)"
                        />
                    </v-list>
                </v-card>
            </v-menu>

            <span
                v-else
                class="text-caption text-medium-emphasis"
            >
                Не найдено
            </span>
        </template>
    </div>
</template>
