<script setup lang="ts">
import AppPrimaryButton from '@/components/UI/AppPrimaryButton/AppPrimaryButton.vue';
import { useHttp } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
const props = defineProps<{
    examId: number | null
}>()

const emit = defineEmits<{
    (e: 'uploaded', document: any):void
}>()

const upload = useHttp<Upload, {isLastChunk:boolean}>({
    chunk:null,
    totalChunks:null,
    order: null
})

const initialize = useHttp<InitializeUpload,{uploadId: number | null}>
({
    totalChunks:null,
    fileName:null,
    fileType:null
})

const save = useHttp({
    documentableId:props.examId,
    uploadId: null,
    documentableType: 'exam',
    type:'video'
})

interface InitializeUpload{
    totalChunks:number | null, 
    fileName: string | null,
    fileType: string | null
}

interface Upload{
    chunk: Blob | null
    totalChunks: number | null
    order: number | null
}

const file = ref<File | null>(null)
const uploadId= ref<number | null>(null)
const progress = ref<number>(0)

const download = async () => {
    if(!file.value) return
    
    uploadId.value = null
    const fileSize = file.value?.size
    const fileName = file.value?.name
    const fileType = file.value?.type

    const chunkSize = 5 * 1024 * 1024;
    const totalChunks = Math.ceil(fileSize / chunkSize);
    
    if(! uploadId.value){
        await  initializeUploading(totalChunks, fileName, fileType)
    }

    if(! uploadId.value){
        return
    }

    let order = 1

    for (let start = 0; start < fileSize; start += chunkSize) {
        const chunk = file.value.slice(start, start + chunkSize)

        upload.chunk = chunk
        upload.totalChunks = totalChunks
        upload.order = order
        
        await upload.post(`/uploads/${uploadId.value}/chunks`, {
            onSuccess(response, httpResponse) {
                if(response.isLastChunk){
                    save.uploadId = response.uploadId
                    console.log(response.uploadId)
                    save.post('/documents/link', {
                        onSuccess(response, httpResponse) {
                            emit('uploaded', response.document)
                        },
                    })
                    file.value = null
                }
                order ++
                progress.value = Math.ceil((order / totalChunks) * 100)
            },
        })
    }   
}

const initializeUploading = async (totalChuncks : number, fileName: string, fileType: string) => {
    initialize.totalChunks = totalChuncks
    initialize.fileName = fileName
    initialize.fileType = fileType

    await initialize.post('/uploads', {
        onSuccess(response, httpResponse) {
            uploadId.value =  response.uploadId
        },
    })

}
watch(() => file, () => {
    progress.value = 0
})
</script>

<template>
    <v-card
    variant="text"
    rounded="xl"
>
    <v-card-text>
        <v-file-upload
            v-model="file"
            inset-file-list
            density="compact"
        />
    </v-card-text>

    <v-expand-transition>
        <div v-if="file">
            <v-divider />

            <v-card-text class="py-6">
                <div class="upload-actions">
                    <div class="d-flex align-center ga-3 mb-4">
                        <v-avatar
                            size="40"
                            color="warning"
                            variant="tonal"
                        >
                            <v-icon icon="mdi-alert-outline" />
                        </v-avatar>

                        <div>
                            <div class="font-weight-medium">
                                Во время загрузки
                            </div>

                            <div class="text-caption text-medium-emphasis">
                                Не закрывайте вкладку до завершения процесса
                            </div>
                        </div>
                    </div>

                    <v-progress-linear
                        v-if="progress"
                        :model-value="progress"
                        rounded
                        height="8"
                        color="warning"
                        class="mb-5"
                    />

                    <div class="d-flex justify-center ga-2">
                        <app-primary-button
                            text="Загрузить"
                            @click="download"
                        />

                        <v-btn
                            variant="text"
                            color="medium-emphasis"
                            @click="file = null"
                        >
                            Отмена
                        </v-btn>
                    </div>
                </div>
            </v-card-text>
        </div>
    </v-expand-transition>
</v-card>
</template>