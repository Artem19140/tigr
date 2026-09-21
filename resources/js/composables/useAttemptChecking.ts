import { AttemptChecking, AttemptConduct } from "@/interfaces/Attempt";
import { AttemptAnswer } from "@/interfaces/Task";
import { ref } from "vue";

const attempt = ref<AttemptChecking | AttemptConduct | null>(null)

export const useAttemptChecking = () => {
    const rated = (taskId: number, attemptAnswer:AttemptAnswer) => {
        if (!attempt.value) return

        const index = attempt.value.tasks.findIndex(t => t.id === taskId)
        if (index === -1) return

        attempt.value.tasks[index] = {
            ...attempt.value.tasks[index],
            attemptAnswer: {
                ...attemptAnswer
            }
        }
    }
    return {rated, attempt}
}