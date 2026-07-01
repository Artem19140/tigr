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

const isOpen = ref<boolean>(false)
</script>

<template>
  <div class="min-h-screen bg-[#F7F9FC] px-8 py-10">

    <div class="mx-auto max-w-5xl space-y-8">

      <div
        class="sticky top-0 z-30 -mx-8 px-8
               bg-[#F7F9FC]/70 backdrop-blur-md
               border-b border-black/5 transition-all duration-300"
      >
        <div
          class="flex items-start justify-between gap-6 py-4"
        >

          <div class="space-y-1 transition-all duration-300">
            <h1
              class="font-semibold tracking-tight text-gray-900
                     text-2xl leading-tight"
            >
              {{ foreignNational?.data.fullName }}
            </h1>

            <div class="flex items-center gap-2 text-sm text-gray-500">
              <span>ID {{ foreignNational?.data.id }}</span>
              <span class="text-gray-300">•</span>
              <span>
                {{ getCountryTitle(foreignNational?.data.citizenship ?? null) }}
              </span>
            </div>
          </div>

          <!-- ACTIONS -->
          <div class="flex items-center gap-3">
            <AppPrimaryButton
              text="Записать"
              @click="isOpen = true"
            />

            <button
              class="px-4 py-2 text-sm text-gray-500
                     hover:text-gray-900 transition"
            >
              Редактировать
            </button>
          </div>

        </div>
      </div>

      <!-- SUMMARY -->
      <section
        class="bg-white/80 backdrop-blur border border-black/5
               rounded-2xl p-8 shadow-sm
               hover:shadow-md hover:-translate-y-[1px]
               transition-all duration-200"
      >
        <div class="grid grid-cols-3 gap-6">
          <div class="text-xs uppercase tracking-wide text-gray-400">
            ФИО (лат.)
          </div>

          <div class="col-span-2 text-sm text-gray-900 font-medium">
            {{ foreignNational?.data.fullNameLatin }}
          </div>
        </div>
      </section>

      <!-- DETAILS -->
      <div class="space-y-6">

        <!-- CARD -->
        <section
          class="bg-white rounded-2xl border border-black/5 p-8 shadow-sm
                 hover:shadow-md hover:-translate-y-[1px]
                 transition-all duration-200"
        >
          <div class="space-y-6">

            <div class="grid grid-cols-3 gap-6">
              <div class="text-xs uppercase tracking-wide text-gray-400">
                Дата рождения
              </div>
              <div class="col-span-2 text-sm text-gray-900">
                {{ new DateFormatter(foreignNational?.data.dateBirth).format('d.m.Y') }}
              </div>
            </div>

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

            <div class="grid grid-cols-3 gap-6">
              <div class="text-xs uppercase tracking-wide text-gray-400">
                Телефон
              </div>
              <div class="col-span-2 text-sm text-gray-900">
                {{ formatPhoneNumber(foreignNational?.data.phone) }}
              </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
              <div class="text-xs uppercase tracking-wide text-gray-400">
                Ответственный
              </div>
              <div class="col-span-2 text-sm text-gray-900">
                {{ foreignNational?.data.creatorFullName }}
              </div>
            </div>

          </div>
        </section>

        <!-- DOCUMENTS -->
        <section
          v-if="foreignNational?.data.permissions.documents"
          class="bg-white rounded-2xl border border-black/5 p-8 shadow-sm
                 hover:shadow-md hover:-translate-y-[1px]
                 transition-all duration-200"
        >
          <h2 class="text-sm font-medium text-gray-900 mb-6 tracking-wide">
            Документы
          </h2>

          <ForeignNationalsDocuments
            :documents="foreignNational.data.documents"
          />
        </section>

        <section
          class="bg-white rounded-2xl border border-black/5 p-8 shadow-sm
                 hover:shadow-md hover:-translate-y-[1px]
                 transition-all duration-200"
        >
          <h2 class="text-sm font-medium text-gray-900 mb-6 tracking-wide">
            Записи ({{ foreignNational.data.enrollments.length }})
          </h2>

          <ForeignNationalEnrollments
            :enrollments="foreignNational.data.enrollments"
          />
        </section>

      </div>
    </div>

    <EnrollmentModal
      v-model="isOpen"
      :foreign-national="foreignNational.data"
    />
  </div>
</template>