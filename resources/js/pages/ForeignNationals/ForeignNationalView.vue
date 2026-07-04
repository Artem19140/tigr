<script setup lang="ts">
import { DateFormatter } from '@/helpers/DateFormatter';
import { ForeignNational } from '@/interfaces/ForeignNational';
import countries from '@data/countries.json'
import ForeignNationalEnrollments from './Components/ForeignNationalEnrollments.vue';
import ForeignNationalsDocuments from './Components/ForeignNationalsDocuments.vue';
import EmployeeLayout from '@/layouts/EmployeeLayout.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import EnrollmentModal from './Components/EnrollmentModal.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
	foreignNational:{
		data: ForeignNational
	}
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
const edit = () => {
  	router.visit(`/foreign-nationals/${props.foreignNational.data.id}/edit`)
}
const isOpen = ref<boolean>(false)
</script>

<template>
  <v-container class="space-y-8 ">
    <div
      class="sticky top-0 z-30 bg-[#F7F9FC]/70 backdrop-blur-md border-b -mx-6"
    >
      <div class="py-4 px-0" >

        <div class="flex items-start justify-between gap-6">

          <div class="space-y-1">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
              {{ foreignNational?.data.fullName }}
            </h1>

            <div class="text-sm text-gray-500 flex items-center gap-2">
              <span>ID {{ foreignNational?.data.id }}</span>
              <span class="text-gray-300">•</span>
              <span>
                {{ getCountryTitle(foreignNational?.data.citizenship ?? null) }}
              </span>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <AppPrimaryButton
              text="Записать"
              @click="isOpen = true"
              v-if="foreignNational.data.permissions.enroll"
            />

            <v-btn
              variant="text"
              class="text-gray-500"
              @click="edit"
              v-if="foreignNational.data.permissions.edit"
            >
              Редактировать
            </v-btn>
          </div>

        </div>

      </div>
    </div>

    <v-card>
      <v-card-text class="pa-8">
        <div class="grid grid-cols-3 gap-6">
          <div class="text-xs uppercase tracking-wide text-gray-400">
            ФИО (лат.)
          </div>

          <div class="col-span-2 text-sm font-medium text-gray-900">
            {{ foreignNational?.data.fullNameLatin }}
          </div>
        </div>
      </v-card-text>
    </v-card>

    <v-card>
      <v-card-text class="pa-8 space-y-6">

        <div class="grid grid-cols-3 gap-6">
          <div class="text-xs uppercase tracking-wide text-gray-400">
            Дата рождения
          </div>
          <div class="col-span-2 text-sm text-gray-900">
            {{ new DateFormatter(foreignNational?.data.dateBirth).format('d.m.Y') }}
          </div>
        </div>

        <v-divider class="opacity-20" />

        <div class="grid grid-cols-3 gap-6">
          <div class="text-xs uppercase tracking-wide text-gray-400">
            Паспорт
          </div>
          <div class="col-span-2 text-sm text-gray-900 leading-relaxed">
            {{ foreignNational?.data.fullPassport }}
            <span class="text-gray-300 mx-1">/</span>
            {{ foreignNational?.data.issuedBy }}
            <span class="text-gray-300 mx-1">/</span>
            {{ new DateFormatter(foreignNational?.data.issuedDate).format('d.m.Y') }}
          </div>
        </div>

        <v-divider class="opacity-20" />

        <div class="grid grid-cols-3 gap-6">
          <div class="text-xs uppercase tracking-wide text-gray-400">
            Телефон
          </div>
          <div class="col-span-2 text-sm text-gray-900">
            {{ formatPhoneNumber(foreignNational?.data.phone) }}
          </div>
        </div>

        <v-divider class="opacity-20" />

        <div class="grid grid-cols-3 gap-6">
          <div class="text-xs uppercase tracking-wide text-gray-400">
            Ответственный
          </div>
          <div class="col-span-2 text-sm text-gray-900">
            {{ foreignNational?.data.creatorFullName }}
          </div>
        </div>

      </v-card-text>
    </v-card>

    <v-card
      v-if="foreignNational?.data.permissions.documents"
    >
      <v-card-text class="pa-8">

        <div class="text-sm font-medium text-gray-900 mb-6">
          Документы
        </div>

        <ForeignNationalsDocuments
          :documents="foreignNational.data.documents"
        />

      </v-card-text>
    </v-card>

    <v-card v-if="foreignNational.data.permissions.enrollments">
      <v-card-text class="pa-8">

        <div class="text-sm font-medium text-gray-900 mb-6">
          Записи ({{ foreignNational.data.enrollments.length }})
        </div>

        <ForeignNationalEnrollments
          :enrollments="foreignNational.data.enrollments"
        />

      </v-card-text>
    </v-card>
  </v-container>
  <EnrollmentModal
    v-model="isOpen"
    :foreign-national="foreignNational.data"
  />
</template>