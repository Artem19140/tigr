<script setup lang="ts">
import BaseThreeDotDropdown from '@/components/BaseComponents/BaseThreeDotDropdown/BaseThreeDotDropdown.vue';
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useSnackbarQueue } from '@/composables/useSnackbarQueue';
import { useHttp } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    documents:any
}>()

const getLabel = (type: string) => {
    switch(type){
        case 'passport_translate':
            return 'Перевод паспорта'
        case 'passport':
            return 'Паспорт'
    }
}

const updatedId = ref<number | null>(null)

const open = (docId :number) => {
    window.open(`/documents/${docId}`)
}

const http = useHttp<{document : File | null}>({
    document:  null
})

const update = (docId :number) => {
    http.put(`/documents/${docId}`, {
        onSuccess(response, httpResponse) {
            const {add} = useSnackbarQueue()
            add('Документ обновлен', 'green')
            clear()
        },
    })
}

const clear = () => {
    updatedId.value = null
    http.document = null
}
</script>

<template>
    <v-list>
        <div
            v-for="doc in documents"
            :key="doc.id"
        >
            <v-list-item
            
                variant="text"
                
            >
                <template v-slot:prepend>
                    <v-img
                        width="35"
                        src="https://cdn-icons-png.flaticon.com/512/9034/9034536.png"
                    />
                </template>

                <v-list-item-title>
                    {{ getLabel(doc.type) }}
                </v-list-item-title>

                <v-list-item-subtitle>
                    {{ doc.createdAt }}
                </v-list-item-subtitle>

                <template v-slot:append>
                    <BaseThreeDotDropdown>
                        <v-list-item
                            @click="() => open(doc.id)"
                        >
                            Открыть
                        </v-list-item>
                        
                        <v-list-item
                            @click="updatedId = doc.id"
                        >
                            Заменить
                        </v-list-item>
                    </BaseThreeDotDropdown>
                </template>
                
            </v-list-item>
            <div v-if="updatedId === doc.id">
                <v-file-upload
                    density="compact" 
                    v-model="http.document"
                    :error-messages="http.errors.document"
                    :readonly="http.processing"
                />
                <div class="flex justify-center gap-4">
                    <AppPrimaryButton 
                        @click="() => update(doc.id)"
                        text="Загрузить"
                        :loading="http.processing"
                        :disabled="http.processing || ! http.document"
                    />
                    <v-btn
                        @click="clear"
                        border
                        variant="text"
                    >    
                        Отмена
                    </v-btn>
                </div>
            </div>
        </div>
    </v-list>

</template>