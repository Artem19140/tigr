<script setup lang="ts">
import { useModals } from '@composables/useModals';
import EmployeeCreateModal from '@pages/Center/Components/Employees/EmployeeCreateModal.vue';
import AddressCreateModal from '@/pages/Center/Components/Addresses/AddressCreateModal.vue';
import EmployeeEditModal from '@/pages/Center/Components/Employees/EmployeeEditModal.vue';
import LogoutAllDevicesModal from './LogoutAllDevicesModal.vue';
import CenterCreateModal from '@/pages/PlatformAdmin/Components/CenterCreateModal.vue';

const {modals, close} = useModals()

export type ModalName = keyof typeof modalMap

const modalComponent = (name: ModalName) => modalMap[name] ?? null

const modalMap = {
    employeeCreate: EmployeeCreateModal,
    addressCreate: AddressCreateModal,
    employeeEdit:EmployeeEditModal,
    logoutAll:LogoutAllDevicesModal,
    centerCreate:CenterCreateModal
} as const

const closeModal = (id: number) => {
    close(id)
}
</script>

<template>
    <component
        v-for="modal in modals"
        :key="modal.id"
        v-bind="modal.data"
        :is="modalComponent(modal.name)"
        v-model = modal.isOpen
        @update:modelValue="closeModal(modal.id)"
    />
</template>