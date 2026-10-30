<script setup lang="ts">
import { onUnmounted, ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import {
    mdiHeadphones,
    mdiCheckCircle,
    mdiVolumeHigh,
    mdiPlay,
    mdiPause,
    mdiArrowRight,
    mdiInformationOutline,
} from '@mdi/js'

const props = defineProps<{
    fullName: string
    infoUrl: string
}>()

const audioPlayer = ref<HTMLAudioElement | null>(null)
const isPlaying = ref(false)
const soundConfirmation = ref(false)
const audioError = ref(false)

const togglePlayPause = async () => {
    const audio = audioPlayer.value
    if (!audio) return

    if (isPlaying.value) {
        audio.pause()
        return
    }

    try {
        audioError.value = false
        await audio.play()
    } catch (error) {
        audioError.value = true
        console.error('Ошибка воспроизведения:', error)
    }
}

const continueToInfo = () => {
    audioPlayer.value?.pause()
    router.visit(props.infoUrl)
}

onUnmounted(() => {
    audioPlayer.value?.pause()
})
</script>

<template>
    <Head title="Настройка звука" />
    <div class="mx-auto w-full max-w-xl px-4 py-8 sm:py-12">
        <audio
            ref="audioPlayer"
            loop
            @play="isPlaying = true"
            @pause="isPlaying = false"
        >
            <source
                src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
                type="audio/mpeg"
            />
        </audio>

        <div class="mb-8 text-center">
            <div
                class="mx-auto mb-5 flex size-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
            >
                <v-icon :icon="mdiHeadphones" size="28" />
            </div>

            <div class="mb-2 text-sm font-medium text-blue-600">
                Подготовка к экзамену
            </div>

            <h1 class="text-2xl font-semibold tracking-tight text-[#101828] sm:text-3xl">
                Добро пожаловать, {{ fullName }}!
            </h1>

            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-[#667085]">
                Перед началом экзамена убедитесь, что звук на вашем устройстве
                работает корректно.
            </p>
        </div>

        <div
            class="rounded-2xl border p-5 transition-colors sm:p-6"
            :class="
                soundConfirmation
                    ? 'border-green-200 bg-green-50/50'
                    : 'border-[#EAECF0] bg-white'
            "
        >
            <div class="flex items-start gap-3">
                <div
                    class="flex size-10 shrink-0 items-center justify-center rounded-xl"
                    :class="
                        soundConfirmation
                            ? 'bg-green-100 text-green-700'
                            : 'bg-blue-50 text-blue-600'
                    "
                >
                    <v-icon
                        :icon="soundConfirmation ? mdiCheckCircle : mdiVolumeHigh"
                        size="22"
                    />
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-base font-semibold text-[#101828]">
                        Проверьте звук
                    </h2>

                    <p class="mt-1 text-sm leading-5 text-[#667085]">
                        Наденьте наушники и включите тестовую запись.
                        Убедитесь, что звук слышен чётко и громкость комфортная.
                    </p>
                </div>
            </div>

            <div class="mt-4 flex items-start gap-2.5 rounded-xl bg-blue-50 p-3">
                <v-icon
                    :icon="mdiInformationOutline"
                    size="20"
                    class="mt-0.5 shrink-0 text-blue-600"
                />

                <p class="text-sm leading-5 text-blue-800">
                    Если звук слишком тихий или громкий, отрегулируйте его
                    кнопками громкости на клавиатуре или самом устройстве.
                </p>
            </div>

            <button
                type="button"
                class="mt-5 flex w-full items-center justify-center gap-2 rounded-xl border border-[#D0D5DD] bg-white px-4 py-3 text-sm font-semibold text-[#344054] transition hover:bg-[#F9FAFB] active:scale-[0.99]"
                @click="togglePlayPause"
            >
                <v-icon
                    :icon="isPlaying ? mdiPause : mdiPlay"
                    size="20"
                />
                {{ isPlaying ? 'Остановить тестовый звук' : 'Воспроизвести тестовый звук' }}
            </button>

            <p
                v-if="audioError"
                class="mt-2 text-sm text-red-600"
            >
                Не удалось воспроизвести звук. Проверьте подключение и попробуйте ещё раз.
            </p>

            <!-- Подтверждение -->
            <div class="mt-4 border-t border-[#EAECF0] pt-3">
                <v-checkbox
                    v-model="soundConfirmation"
                    color="success"
                    hide-details
                    density="comfortable"
                    class="!mt-0"
                >
                    <template #label>
                        <span class="text-sm text-[#344054]">
                            Звук слышен чётко, громкость комфортная
                        </span>
                    </template>
                </v-checkbox>
            </div>
        </div>

        <div class="mt-6">
            <v-btn
                color="primary"
                size="large"
                block
                height="48"
                rounded="lg"
                :disabled="!soundConfirmation"
                @click="continueToInfo"
            >
                Продолжить
                <v-icon :icon="mdiArrowRight" end />
            </v-btn>

            <p class="mt-3 text-center text-xs text-[#98A2B3]">
                Далее вы ознакомитесь с информацией и правилами экзамена
            </p>
        </div>
    </div>
</template>