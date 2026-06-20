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
    <v-card>

        <v-card-text>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <v-card-title>
                        {{ title ?? 'Таблица' }}
                        <slot name="toolbar-left"></slot>
                    </v-card-title>
                    
                </div>
                <div>
                    <slot name="toolbar-actions" />
                </div>
                
            </div>
        </v-card-text>
        <v-data-table
            :items="elements?.data"
            :headers="headers"
            @click:row="(event :Event, { item } : any) => emit('row-click', item)"
            key="id"
            hover
            :loading="loading"
        >
            
            <template
                v-for="(_, slotName) in $slots"
                #[slotName]="slotProps"
            >
                <slot :name="slotName" v-bind="slotProps" />
            </template>

            <template #bottom>
                <AppPaginator v-if="elements"
                    :meta="elements.meta"
                    :links="elements.links"
                    :loading="loading"
                />
            </template>
        </v-data-table>
    </v-card>
</template>