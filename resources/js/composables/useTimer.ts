import {  ref, watch } from "vue"
import { useAttempt } from "./useAttempt"

const timeLeft = ref<number>(0)
const canFinish = ref<boolean>(false)

export const useTimer = (

) => {
    const {examAttempt} = useAttempt()
    const clientNow = () :number  => Math.floor(Date.now() / 1000)  
    const offset = ref(0)

    watch(
        () => examAttempt.value?.serverNow,
        (serverNow) => {
            if (serverNow != null) {
            offset.value = serverNow - clientNow()
            }
        },
        { immediate: true }
    )
    
    let interval: number | null = null

    const calculateTime = () => {
        const end = examAttempt.value?.endsAt ?? null
        if(end === null) return 
        
        const now = clientNow() + offset.value
        timeLeft.value = Math.max(0, end - now)

        if(timeLeft.value <= 0){
            stopTimer()
        }
        
        if(canFinish.value) return 

        const start =  examAttempt.value?.startedAt
        const minDuration  = examAttempt.value?.minDurationMinutes  ?? null

        if (start == null || minDuration == null) return
        
        const diff = now - start
        const duration = Math.floor(diff / 60)

        if(duration >= minDuration){
            canFinish.value = true
        }
    }

    const handleVisibilityChange = () => {
        if (!document.hidden) {
            calculateTime()
        }
    }

    const startTimer = () => {
        if (interval) return

        calculateTime()
        interval = window.setInterval(calculateTime, 1000)

        document.addEventListener('visibilitychange', handleVisibilityChange)
    }

    const stopTimer = () => {
        if (interval) clearInterval(interval)
        interval = null

        document.removeEventListener('visibilitychange', handleVisibilityChange)
    }

    return {startTimer, stopTimer, canFinish, timeLeft}
}