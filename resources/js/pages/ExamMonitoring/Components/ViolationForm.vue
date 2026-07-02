<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';

const props = defineProps<{
    loading:boolean,
    errorMessages:string | undefined
}>()

const comment = defineModel<string | null>({default:null})

const emit = defineEmits<{
    (e:'save'):void,
    (e:'cancel'):void
}>()
</script>

<template>
    <v-textarea 
        class="mt-2"
        label="Нарушение"
        hint="Максимум 256 символов"
        maxlength="256"
        v-model="comment"
        auto-grow
        rows="1"
        :error-messages="errorMessages"
    />
    <AppPrimaryButton 
        text="Сохранить"
        :loading="loading"
        @click="emit('save')"
    />

    <v-btn variant="text" @click="emit('cancel')">
        Отмена
    </v-btn>
</template>