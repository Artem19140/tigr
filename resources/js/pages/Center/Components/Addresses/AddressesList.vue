<script setup lang="ts">
import { ref, watch } from 'vue';
import AppAddButton from '@/components/UI/AppAddButton/AppAddButton.vue';
import { useModals } from '@/composables/useModals';
import AddressCard from './AddressCard.vue';
import { Address } from '@/interfaces/Address';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{
    addresses:Address[]
}>()
watch(() => props.addresses, (value) => {
    addresses.value = value
})

const addresses = ref<Address[]>(props.addresses)

watch(() => props.addresses, () => {
    addresses.value = props.addresses
})

addresses.value.map(v => v.loading = false)

const add = () => {
    const {open} = useModals()
    open('addressCreate')
}

</script>

<template>
    <Head>
        <title>Адреса</title>
    </Head>
    <v-card
        rounded="xl"
        class="address-list"
    >

        <div class="address-header">

            <div class="d-flex justify-end">
                <AppAddButton
                    @click="add"
                />
            </div>

        </div>

        <v-divider v-if="addresses.length > 0" />

        <div v-if="addresses.length > 0" class="address-items">

            <AddressCard
                v-for="address in addresses"
                :key="address.id"
                :address="address"
            />

        </div>

        <v-empty-state
            v-else
            icon="mdi-clipboard-text-off-outline"
            title="Адресов пока нет"
            class="py-10"
        >
            <AppAddButton
                @click="add"
            />
        </v-empty-state>

    </v-card>
</template>

<style lang="css" scoped>
.address-header {
    padding: 12px 16px;
}

.address-items {
    display: flex;
    flex-direction: column;
    gap: 12px;

    padding: 16px;
}
</style>