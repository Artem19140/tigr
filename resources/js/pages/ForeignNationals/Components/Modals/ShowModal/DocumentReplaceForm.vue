<script setup lang="ts">
const props = defineProps<{
    documentId: any
}>()
</script>

<template>
    <v-expand-transition>
    <div class="px-4 pb-4">
        <v-sheet
            rounded="lg"
            border
            class="pa-4"
        >
            <v-file-upload
                v-model="document"
                density="compact"
            />

            <div class="d-flex justify-end ga-2 mt-4">
                <v-btn
                    variant="text"
                    @click="$emit('cancel')"
                >
                    Отмена
                </v-btn>

                <AppPrimaryButton
                    text="Загрузить"
                />
            </div>
        </v-sheet>
    </div>
</v-expand-transition>
</template>