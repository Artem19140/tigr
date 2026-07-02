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
    <v-card
        class="base-table"
    >
        <div class="table-header">
            <div class="d-flex align-center justify-space-between flex-wrap ga-3">

                <div class="d-flex align-center ga-3 min-w-0">
                    <div class="text-subtitle-1 font-weight-medium text-truncate">
                        {{ title }}
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
</style>