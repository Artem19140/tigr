import { ref } from "vue";

const audioPlayingUrl = ref<string | null>(null)

export const useAudioPlaying = ()  => {
    const audioStartPlaying = (url: string) => {
        audioPlayingUrl.value = url
        
    }

    const audioStopPlaying = () => {
        audioPlayingUrl.value = null
    }

     return {
        audioPlayingUrl, 
        audioStartPlaying, 
        audioStopPlaying
    }
}