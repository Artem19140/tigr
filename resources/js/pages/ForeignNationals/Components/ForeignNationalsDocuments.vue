<script setup lang="ts">
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import { router, useHttp } from '@inertiajs/vue3';
import { mdiFileDocumentOutline } from '@mdi/js';
import { ref } from 'vue';

const props = defineProps<{
    documents:any
}>()

const getLabel = (type: string) => {
  switch(type){
    case 'passport_translate':
        return 'Перевод паспорта'
    case 'passport':
        return 'Паспорт'
  }
}

const updatedId = ref<number | null>(null)

const open = (docId :number) => {
    window.open(`/documents/${docId}`)
}

const http = useHttp<{document : File | null}>({
  	document:  null
})

const update = (docId :number) => {
  	http.put(`/documents/${docId}`, {
		onSuccess(response, httpResponse) {
			const {add} = useSnackbarQueue()
			add('Документ обновлен', 'green')
			clear()
			router.reload()
		},
	})
}

const clear = () => {
	updatedId.value = null
	http.document = null
}
</script>

<template>
  <v-list class="bg-transparent">
    <div
      v-for="doc in documents"
      :key="doc.id"
      class="rounded-xl px-3 py-2"
    >
      <v-list-item class="px-0">
        <template #prepend>
          <v-avatar
            size="40"
            rounded="lg"
            class="bg-slate-100 text-slate-600"
          >
            <v-icon :icon="mdiFileDocumentOutline" />
          </v-avatar>
        </template>
		
        <v-list-item-title class="text-sm font-medium text-slate-900">
          {{ getLabel(doc.type) }}
        </v-list-item-title>

        <v-list-item-subtitle class="text-xs text-slate-500">
          {{ doc.createdAt }}
        </v-list-item-subtitle>

        <template #append>
          <div class="opacity-60 hover:opacity-100 transition">
            <BaseThreeDotDropdown>
              <v-list-item
                class="text-sm"
                @click="() => open(doc.id)"
              >
                Открыть
              </v-list-item>

              <v-list-item
                v-if="doc.permissions.update"
                class="text-sm"
                @click="updatedId = doc.id"
              >
                Заменить
              </v-list-item>
            </BaseThreeDotDropdown>
          </div>
        </template>
      </v-list-item>

      <div
        v-if="updatedId === doc.id"
        class="mt-3 rounded-lg border border-dashed border-slate-200 bg-white/60 p-3 backdrop-blur"
      >
        <v-file-upload
          density="compact"
          v-model="http.document"
          :error-messages="http.errors.document"
          :readonly="http.processing"
          class="mb-3"
        />

        <div class="flex items-center justify-end gap-3">
          <v-btn
            variant="text"
            class="text-slate-500"
            @click="clear"
          >
            Отмена
          </v-btn>

          <AppPrimaryButton
            text="Загрузить"
            :loading="http.processing"
            :disabled="http.processing || !http.document"
            @click="() => update(doc.id)"
          />
        </div>
      </div>
    </div>
  </v-list>
</template>