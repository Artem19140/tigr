<script setup lang="ts">
const props = defineProps<{
    elements?: Array<Object>,
    headers: Array<any>,
    title?:string
    loading?:boolean,
}>()

const emit = defineEmits<{
    (e: 'row-click', item: any): void
}>()
</script>

<template>
    <v-card>
        <div class="p-4">
            <div class="d-flex align-center flex-wrap ga-3">

                <div class="text-subtitle-1 font-weight-medium text-truncate">
                    {{ title }}
                </div>

                <div class="d-flex align-center min-w-0">
                    <slot name="header-left" />
                </div>

                <div class="flex align-center ga-2 ">
                    <slot name="header-actions" />
                </div>

            </div>
        </div>

        <v-divider />

        <v-data-table
            :items="elements"
            :headers="headers"
            :loading="loading"
            hover
            @click:row="(event :Event, { item } : any) => emit('row-click', item)"
            hide-default-footer
            :items-per-page="-1"
        >
            <template
                v-for="(_, slotName) in $slots"
                #[slotName]="slotProps"
            >
                <slot :name="slotName" v-bind="slotProps" />
            </template>

        </v-data-table>
    </v-card>
</template>