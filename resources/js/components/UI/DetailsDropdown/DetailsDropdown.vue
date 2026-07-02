<script setup lang="ts">
import { mdiEye } from '@mdi/js'
</script>

<template>
	<v-menu>
		<template v-slot:activator="{ props }">
			<v-btn 
				:icon="mdiEye" 
				variant="text" 
				v-bind="props" 
				size="small"
			/>
		</template>
		<v-card>
			<v-card-text>
				<slot />
			</v-card-text>
		</v-card>
	</v-menu>
</template>