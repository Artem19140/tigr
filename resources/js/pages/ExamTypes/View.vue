
<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import EmployeeLayout from '@/layouts/EmployeeLayout.vue'
import {
    mdiChevronRight,
    mdiFileDocumentOutline,
    mdiFolderOutline,
} from '@mdi/js'

defineOptions({
    layout: [EmployeeLayout],
})

interface Task {
    id: number
    order: number
    postscriptum: string | null
    description: string | null,
    showUrl: string
}

interface Subblock {
    id: number
    name: string
    minMark: number | null
    tasks: Task[]
}

interface Block {
    id: number
    name: string
    minMark: number
    subblocks: Subblock[]
    backUrl?: string
}

interface ExamType {
    id: number
    name: string
    blocks: Block[]
}

const props = defineProps<{
    type: {
        data: ExamType
    }
}>()

</script>

<template>
    <v-container class="py-8">
        <div class="mx-auto max-w-4xl space-y-6">

            <header class="mb-8">
                <h1 class="text-2xl font-semibold tracking-tight text-gray-900">
                    {{ props.type.data.name }}
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Учебные блоки и задания
                </p>
            </header>


            <v-card
                v-for="block in props.type.data.blocks"
                :key="block.id"
            >

                <div class="flex items-start gap-4 px-5 py-5 sm:px-6">
                    <div
                        class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gray-100"
                    >
                        <v-icon
                            :icon="mdiFolderOutline"
                            size="21"
                            color="grey-darken-2"
                        />
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="break-words text-base font-semibold text-gray-900">
                            {{ block.name }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Минимальная оценка:
                            <span class="font-medium text-gray-700">
                                {{ block.minMark }} баллов
                            </span>
                        </p>
                    </div>
                </div>

                <v-divider />

                <div class="space-y-5 bg-gray-50/60 px-5 py-5 sm:px-6">
                    <template v-if="block.subblocks.length">
                        <section
                            v-for="subblock in block.subblocks"
                            :key="subblock.id"
                            class="relative pl-5"
                        >

                            <div
                                class="absolute bottom-3 left-0 top-3 w-px bg-gray-200"
                            />

                            <div class="relative">

                                <div
                                    class="absolute -left-5 top-4 h-px w-3 bg-gray-200"
                                />

                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex size-8 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white"
                                    >
                                        <v-icon
                                            :icon="mdiFolderOutline"
                                            size="17"
                                            color="grey-darken-1"
                                        />
                                    </div>

                                    <div class="min-w-0 flex-1 py-1">
                                        <h3 class="break-words text-sm font-semibold text-gray-800">
                                            {{ subblock.name }}
                                        </h3>

                                        <p
                                            v-if="subblock.minMark !== null"
                                            class="mt-1 text-xs text-gray-500"
                                        >
                                            Минимальная оценка:
                                            {{ subblock.minMark }} баллов
                                        </p>
                                    </div>

                                    <span
                                        class="shrink-0 rounded-md bg-white px-2 py-1 text-xs text-gray-500 ring-1 ring-gray-200"
                                    >
                                        {{ subblock.tasks.length }}
                                    </span>
                                </div>

                                <div
                                    v-if="subblock.tasks.length"
                                    class="ml-4 mt-3 space-y-1 border-l border-gray-200 pl-4"
                                >
                                    <Link
                                        v-for="task in subblock.tasks"
                                        :key="task.id"
                                        :href="task.showUrl"
                                        class="group flex items-center gap-3 rounded-lg px-3 py-3 transition-colors hover:bg-white hover:shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-400"
                                    >
                                        <v-icon
                                            :icon="mdiFileDocumentOutline"
                                            size="18"
                                            color="grey"
                                            class="shrink-0"
                                        />

                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-700 transition-colors group-hover:text-gray-900">
                                                Задание {{ task.order }}
                                            </p>

                                            <p
                                                v-if="task.description || task.postscriptum"
                                                class="mt-1 line-clamp-2 text-xs leading-relaxed text-gray-500"
                                            >
                                                {{ task.description || task.postscriptum }}
                                            </p>
                                        </div>

                                        <v-icon
                                            :icon="mdiChevronRight"
                                            size="18"
                                            color="grey"
                                            class="shrink-0 opacity-0 transition-all group-hover:translate-x-0.5 group-hover:opacity-100"
                                        />
                                    </Link>
                                </div>

                                <p
                                    v-else
                                    class="ml-4 mt-3 border-l border-gray-200 py-2 pl-4 text-xs text-gray-400"
                                >
                                    Заданий пока нет
                                </p>
                            </div>
                        </section>
                    </template>

                    <p
                        v-else
                        class="py-3 text-center text-sm text-gray-400"
                    >
                        В этом блоке пока нет подблоков
                    </p>
                </div>
            </v-card>

            <div
                v-if="!props.type.data.blocks.length"
                class="rounded-xl border border-dashed border-gray-300 px-6 py-12 text-center"
            >
                <v-icon
                    :icon="mdiFolderOutline"
                    size="32"
                    color="grey-lighten-1"
                />

                <p class="mt-3 text-sm font-medium text-gray-700">
                    Блоков пока нет
                </p>

                <p class="mt-1 text-sm text-gray-500">
                    Учебные материалы появятся здесь позже
                </p>
            </div>
        </div>
    </v-container>
</template>
