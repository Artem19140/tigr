import { Attempt } from "@/interfaces/Attempt";
import { AttemptAnswer } from "@/interfaces/Task";
import { ref } from "vue";

const examAttempt = ref<Attempt | null>(null)
const errors = ref<Set<number>>(new Set())

export const useAttempt = ()  => {
    const loading= ref<boolean>(true)
    const updateAnswer = (taskId: number, attemptAnswer: AttemptAnswer) => {
        if (!examAttempt.value) return

        const index = examAttempt.value.tasks.findIndex(t => t.id === taskId)
        if (index === -1) return

        examAttempt.value.tasks[index] = {
            ...examAttempt.value.tasks[index],
            attemptAnswer: {
                ...attemptAnswer
            }
        }
    }

    const setError = (taskId:number) => {
        errors.value.add(taskId)
    }

    const removeError = (taskId:number) => {
        errors.value.delete(taskId)
    }    
    return {
            updateAnswer, 
            examAttempt, 
            errors, 
            setError,
            removeError,
            loading
        }
}
