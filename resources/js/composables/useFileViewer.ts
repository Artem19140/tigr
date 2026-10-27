import { ref } from "vue";

const fileUrl = ref<string | undefined>(undefined)
const isOpen = ref<boolean>(false)

export const useFileViewer = () => {

    const getUrl = () => {
        return fileUrl.value
    }

    const open = (url: string) => {
        isOpen.value = true
        fileUrl.value = url
    }

    const close = () => {
        isOpen.value = false
        fileUrl.value = undefined
    }

    return {
        open, 
        close,
        isOpen,
        getUrl
    }
}