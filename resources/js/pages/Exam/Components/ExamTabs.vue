<script setup lang="ts">
import { computed, ref, } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
  examId:number,
  tab?:string,
  actions?:any
}>()

const tab = ref<string | undefined>(props.tab)

const tabs = [
    {
        visible: true,
        tab:"",
        title:'Основное'
    },
    {
        visible: props.actions.conduct.can,
        tab:"conduct",
        title:"Проведение"
    },
    {
        visible: props.actions.check.can,
        tab:"check",
        title:"Проверка"
    }
]

const visibleTabs = computed(() => {
    return tabs.filter(tab => tab.visible === true)
})

const visit = (mode: string) => {
    const url = tab ? `/exams/${props.examId}/${mode}` : `/exams/${props.examId}`
    router.visit(url, {
        replace:true
    })
}
</script>

<template>
    <div class="mt-3">
        
        <v-tabs v-model="tab">
            <v-tab
                v-for="(tab, index) in visibleTabs"
                :key="index"
                class="text-sm tracking-wide"
                :value="tab.tab"
                @click="() => visit(tab.tab)"
            >
                {{ tab.title }}
            </v-tab>

        </v-tabs>
    </div>
</template>