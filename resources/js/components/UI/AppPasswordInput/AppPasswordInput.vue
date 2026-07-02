<script setup lang="ts">
import { mdiEyeOff, mdiEye } from '@mdi/js'
import { ref } from 'vue';

const show = ref<boolean>(false);
const props = defineProps<{
    label?:string,
    placeholder?:string
}>()
</script>

<template>
    <v-text-field
        :append-inner-icon="show ? mdiEyeOff : mdiEye"
        :type="show ? 'text' : 'password'"
        name="password"
        :label="label ?? 'Пароль'"
        @click:append-inner="show = !show"
        :placeholder=" placeholder ?? 'Введите пароль'"
    />
</template>