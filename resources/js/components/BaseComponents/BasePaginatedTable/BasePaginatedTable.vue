<script setup lang="ts">
import AppPaginator from '@/components/UI/AppPaginator/AppPaginator.vue';
import { Paginated } from '@interfaces/Interfaces';
const props = defineProps<{
    elements?: Paginated<any>,
    headers: Array<any>,
    title?:string
    loading?:boolean,
}>()

const emit = defineEmits<{
    (e: 'row-click', item: any): void
}>()
</script>

<template>
    <v-card
        class="base-table"
        rounded="xl"
    >
        <div class="table-header">
            <div class="d-flex align-center justify-space-between flex-wrap ga-3">
                <div class="d-flex align-center ga-3 min-w-0">
                    <div class="text-subtitle-1 font-weight-medium text-truncate">
                        {{ title ?? 'Таблица' }}
                    </div>

                    <slot name="header-left" />
                </div>
                <div class="d-flex align-center ga-2">
                    <slot name="header-actions" />
                </div>

            </div>
        </div>

        <v-divider />

        <v-data-table
            class="modern-table"
            :items="elements?.data"
            :headers="headers"
            :loading="loading"
            hover
            @click:row="(event :Event, { item } : any) => emit('row-click', item)"
        >
            <template
                v-for="(_, slotName) in $slots"
                #[slotName]="slotProps"
            >
                <slot :name="slotName" v-bind="slotProps" />
            </template>

            <template #bottom>
                <div class="table-footer">
                    <AppPaginator
                        v-if="elements"
                        :meta="elements.meta"
                        :links="elements.links"
                        :loading="loading"
                    />
                </div>
            </template>
        </v-data-table>
    </v-card>
</template>

<style lang="css" scoped>
.base-table {
    overflow: hidden;
    background: rgba(var(--v-theme-surface), 0.9);
    backdrop-filter: blur(6px);
}

.table-header {
    padding: 14px 16px;
    position: sticky;
    top: 0;
    z-index: 2;
    backdrop-filter: blur(10px);
}

.modern-table {
    background: transparent;
}

.modern-table :deep(tbody tr:hover) {
    cursor: pointer;
    background: rgba(var(--v-theme-on-surface), 0.04);
}

.table-footer {
    padding: 12px 16px;
    border-top: 1px solid rgba(var(--v-border-color), 0.08);
}
</style>