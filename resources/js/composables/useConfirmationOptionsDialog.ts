import {  ref, watch } from 'vue'
const isOpen = ref<boolean>(false)
const message = ref<string | null>(null)
const confirmation = ref<boolean>(false)
const confirmationError = ref<boolean>(false)
let resolvePromise:((value : boolean) => void) | undefined;
watch(() => confirmation.value, (confimation) => {
    if(confimation){
        confirmationError.value = false
    }
})
export const useConfirmationOptionsDialog = () => {
    const open = (text:string = '') =>{
        isOpen.value = true
        message.value = text
        confirmationError.value = false
        confirmation.value = false
        return new Promise<boolean>((resolve) => {
            resolvePromise = resolve
        })
    }

    const ok = () => {
        if(!confirmation.value){
            confirmationError.value = true
            return
        }
        isOpen.value = false
        message.value = null
        resolvePromise?.(true)
        resolvePromise = undefined
    }

    const close = () => {
        isOpen.value = false
        message.value = null
        resolvePromise?.(false)
        resolvePromise = undefined
    }

    return {open, close,ok, confirmation, confirmationError, isOpen, message}
}
