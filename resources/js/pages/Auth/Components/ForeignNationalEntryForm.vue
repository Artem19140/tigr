<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AppPrimaryButton from '@components/UI/AppPrimaryButton/AppPrimaryButton.vue'

const form = useForm<{code:string | null}>({
  code: null,
})

const submit = () => {
  form.post('/exam-codes/verify', {
    preserveScroll: true,
  })
}
</script>

<template>
    <v-form @submit.prevent="submit">
        <div class="space-y-5">
            <div class="flex justify-center">
                <v-otp-input
                    v-model="form.code"
                    type="number"
                >
                    <template #fields>
                        <v-otp-group merged>
                            <v-otp-field :index="0" />
                            <v-otp-field :index="1" />
                            <v-otp-field :index="2" />
                        </v-otp-group>

                        <v-otp-separator>-</v-otp-separator>

                        <v-otp-group merged>
                            <v-otp-field :index="3" />
                            <v-otp-field :index="4" />
                            <v-otp-field :index="5" />
                        </v-otp-group>
                    </template>
                </v-otp-input>
            </div>

            <div
                v-if="form.errors.code"
                class="rounded-lg bg-red-50 px-4 py-3 text-center text-sm text-red-600"
            >
                {{ form.errors.code }}
            </div>

            <AppPrimaryButton
                text="Войти"
                type="submit"
                block
                :loading="form.processing"
                :disabled="
                    form.processing ||
                    (form.code?.length ?? 0) < 6
                "
                class="w-full"
            />
        </div>
    </v-form>
</template>