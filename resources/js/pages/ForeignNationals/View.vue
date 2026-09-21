<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { ForeignNational } from '@/interfaces/ForeignNational';
import countries from '@data/countries.json'
import ForeignNationalEnrollments from './Components/ForeignNationalEnrollments.vue';
import ForeignNationalsDocuments from './Components/ForeignNationalsDocuments.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import EnrollmentModal from './Components/EnrollmentModal.vue';
import { computed, ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps<{
	foreignNational:{
		data: ForeignNational
	},
  editUrl: string
}>()

defineOptions({
  	layout: [EmployeeLayout]
})

const getCountryTitle = (value:string | null) => {
	const result = countries.find(item => item.value === value);
	return result ? result.text : '-';
}

function formatPhoneNumber(cleaned: string | null) {
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
const isOpen = ref<boolean>(false)

const personalData = computed(() => [
    {
        label: 'ФИО (лат.)',
        value: props.foreignNational.data.fullNameLatin,
    },
    {
        label: 'Дата рождения',
        value: new DateFormatter(
            props.foreignNational.data.dateBirth ?? ''
        ).format('d.m.Y'),
    },
    {
        label: 'Паспорт',
        value: [
            props.foreignNational.data.fullPassport,
            props.foreignNational.data.issuedBy,
            props.foreignNational.data.issuedDate
                ? new DateFormatter(
                    props.foreignNational.data.issuedDate
                ).format('d.m.Y')
                : null,
        ]
            .filter(Boolean)
            .join(' / '),
    },
    {
        label: 'Телефон',
        value: formatPhoneNumber(props.foreignNational.data.phone),
    },
    {
        label: 'Ответственный',
        value: props.foreignNational.data.creatorFullName,
    },
])
</script>

<template>
    <Head :title="foreignNational.data.fullName" />

    <v-container>
        <div class="mx-auto max-w-4xl space-y-5">

            <div class="flex items-start justify-between gap-6">
                <div class="min-w-0">
                    <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                        {{ foreignNational.data.fullName }}
                    </h1>

                    <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                        <span>ID {{ foreignNational.data.id }}</span>
                        <span class="text-gray-300">•</span>
                        <span>
                            {{ getCountryTitle(foreignNational.data.citizenship ?? null) }}
                        </span>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <v-btn
                        v-if="editUrl"
                        variant="text"
                        @click="router.visit(editUrl, { replace: true })"
                    >
                        Редактировать
                    </v-btn>

                    <AppPrimaryButton
                        v-if="foreignNational.data.permissions.enroll"
                        text="Записать"
                        @click="isOpen = true"
                    />
                </div>
            </div>

            <!-- Personal data -->
            <v-card class="overflow-hidden rounded-xl">
                <v-card-text class="px-6 py-2">
                    <div
                        v-for="(item, index) in personalData"
                        :key="item.label"
                        class="grid grid-cols-1 gap-1 py-4 sm:grid-cols-[180px_1fr] sm:gap-6"
                        :class="{
                            'border-b border-gray-100': index !== personalData.length - 1
                        }"
                    >
                        <div class="text-sm font-medium text-gray-500">
                            {{ item.label }}
                        </div>

                        <div class="break-words text-sm font-medium text-gray-900">
                            {{ item.value || '—' }}
                        </div>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Documents -->
            <v-card
                v-if="foreignNational.data.permissions.documents"
                class="overflow-hidden rounded-xl"
            >
                <v-card-text class="px-6 pt-6">
                    <div class="text-lg font-semibold text-gray-900">
                        Документы
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        Загруженные документы иностранного гражданина.
                    </div>
                </v-card-text>

                <v-card-text class="px-6 pb-6">
                    <ForeignNationalsDocuments
                        :documents="foreignNational.data.documents"
                    />
                </v-card-text>
            </v-card>

            <!-- Enrollments -->
            <v-card
                v-if="foreignNational.data.permissions.enrollments"
                class="overflow-hidden rounded-xl"
            >
                <v-card-text class="px-6 pt-6">
                    <div class="text-lg font-semibold text-gray-900">
                        Записи
                        <span class="font-normal text-gray-400">
                            ({{ foreignNational.data.enrollments.length }})
                        </span>
                    </div>

                    <div class="mt-1 text-sm text-gray-500">
                        История записей на экзамены.
                    </div>
                </v-card-text>

                <v-card-text class="px-6 pb-6">
                    <ForeignNationalEnrollments
                        :enrollments="foreignNational.data.enrollments"
                    />
                </v-card-text>
            </v-card>
        </div>
    </v-container>

    <EnrollmentModal
        v-model="isOpen"
        :foreign-national="foreignNational.data"
    />
</template>