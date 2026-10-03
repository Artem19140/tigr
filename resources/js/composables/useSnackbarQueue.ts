import { ref } from "vue";

const messages = ref<SnackBar[]>([])
const queue = ref()
export const useSnackbarQueue = () => {
    const add = (
        text:string, 
        color:string,  
        prependIcon?:string,
        timeout = 5000,
    ) => {
        
        messages.value.push({
            text,
            color,
            timeout,
            prependIcon:prependIcon ?? ''
        })
    }

    return {messages, queue, add}
}

type SnackBar = {
    text:string,
    color:string,
    timeout:number,
    prependIcon:string | null
}