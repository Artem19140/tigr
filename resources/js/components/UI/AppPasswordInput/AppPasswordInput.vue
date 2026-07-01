<script setup lang="ts">
import { ref } from 'vue';
const show = ref<boolean>(false);
const props = defineProps<{
    label?:string,
    placeholder?:string
}>()
</script>

<template>
    <v-text-field
        :append-inner-icon="show ? 'mdi-eye-off' : 'mdi-eye'"
        :type="show ? 'text' : 'password'"
        name="password"
        :label="label ?? 'Пароль'"
        @click:append-inner="show = !show"
        :placeholder=" placeholder ?? 'Введите пароль'"
    />
</template>