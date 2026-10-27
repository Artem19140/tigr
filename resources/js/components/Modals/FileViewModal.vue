<script setup lang="ts">
import { useFileViewer } from '@/composables/useFileViewer';
import { mdiClose, mdiFileDocumentOutline, mdiOpenInNew } from '@mdi/js';

const {isOpen, getUrl, close} = useFileViewer()
</script>

<template>
  <v-dialog
    v-model="isOpen"
    max-width="1100"
    width="calc(100% - 32px)"
  >
    <v-card>
      <div
        class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 py-3"
      >
        <div class="flex min-w-0 items-center gap-3">
          <div
            class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gray-100"
          >
            <v-icon
              :icon="mdiFileDocumentOutline"
              size="22"
              color="grey-darken-2"
            />
          </div>

          <div class="min-w-0">
            <div class="truncate text-base font-semibold text-gray-900">
              Просмотр документа
            </div>

            <div class="text-xs text-gray-500">
              Предпросмотр файла
            </div>
          </div>
        </div>

        <div class="flex shrink-0 items-center gap-1">
          <v-tooltip text="Открыть в новой вкладке">
            <template #activator="{ props: tooltipProps }">
              <v-btn
                v-bind="tooltipProps"
                :icon="mdiOpenInNew"
                variant="text"
                density="comfortable"
                :href="getUrl()"
                target="_blank"
                rel="noopener noreferrer"
                :disabled="!getUrl()"
                aria-label="Открыть файл в новой вкладке"
              />
            </template>
          </v-tooltip>

          <v-btn
            :icon="mdiClose"
            variant="text"
            density="comfortable"
            aria-label="Закрыть"
            @click="close"
          />
        </div>
      </div>

      <div class="bg-gray-100 p-2 sm:p-4">
        <div
          class="overflow-hidden rounded-lg border border-gray-200 bg-white"
        >
          <iframe
            v-if="getUrl()"
            :src="getUrl()"
            title="Предпросмотр документа"
            class="block h-[70vh] min-h-[360px] w-full sm:h-[75vh]"
            frameborder="0"
            allowfullscreen
          />
        </div>
      </div>
    </v-card>
  </v-dialog>
</template>