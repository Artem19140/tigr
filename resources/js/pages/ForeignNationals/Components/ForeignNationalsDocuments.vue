<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import { router, useHttp } from '@inertiajs/vue3';
import { mdiDownload, mdiFileDocumentOutline, mdiPencil } from '@mdi/js';
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
            class="border-b border-gray-100 last:border-0"
        >
            <v-list-item class="px-0 py-3">
                <template #prepend>
                    <div class="mr-3 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-500">
                        <v-icon
                            :icon="mdiFileDocumentOutline"
                            size="20"
                        />
                    </div>
                </template>

                <v-list-item-title class="text-sm font-medium text-gray-900">
                    {{ getLabel(doc.type) }}
                </v-list-item-title>

                <v-list-item-subtitle class="mt-0.5 text-xs text-gray-500">
                    {{ doc.createdAt }}
                </v-list-item-subtitle>

                <template #append>
                    <div class="flex items-center gap-1">
                        <v-tooltip text="Скачать" location="top">
                            <template #activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    :icon="mdiDownload"
                                    variant="text"
                                    density="comfortable"
                                    size="small"
                                    color="grey-darken-1"
                                    @click="open(doc.id)"
                                />
                            </template>
                        </v-tooltip>

                        <v-tooltip
                            v-if="doc.permissions.update"
                            text="Заменить"
                            location="top"
                        >
                            <template #activator="{ props }">
                                <v-btn
                                    v-bind="props"
                                    :icon="mdiPencil"
                                    variant="text"
                                    density="comfortable"
                                    size="small"
                                    color="grey-darken-1"
                                    @click="updatedId = doc.id"
                                />
                            </template>
                        </v-tooltip>
                    </div>
                </template>
            </v-list-item>

            <div
                v-if="updatedId === doc.id"
                class="mb-3 rounded-xl border border-gray-200 bg-gray-50 p-4"
            >
                <div class="mb-3 text-sm font-medium text-gray-700">
                    Заменить документ
                </div>

                <v-file-upload
                    v-model="http.document"
                    density="compact"
                    :error-messages="http.errors.document"
                    :readonly="http.processing"
                    class="mb-4"
                />

                <div class="flex justify-end gap-2">
                    <v-btn
                        variant="text"
                        :disabled="http.processing"
                        @click="clear"
                    >
                        Отмена
                    </v-btn>

                    <AppPrimaryButton
                        text="Загрузить"
                        :loading="http.processing"
                        :disabled="http.processing || !http.document"
                        @click="update(doc.id)"
                    />
                </div>
            </div>
        </div>
    </v-list>
</template>