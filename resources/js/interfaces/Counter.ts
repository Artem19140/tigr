export interface Counter{
    id: number,
    key: string,
    value: number,
    nextValue: number,
    updateUrl: string
    lastIncrementAt: string | null
}