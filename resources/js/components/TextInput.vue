<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-md border-gray-100 shadow-sm focus:ring-2 bg-white outline-none focus:outline-none focus:border-0 focus:ring-[#8726d5] min-h-10 px-3 py-2 text-sm leading-5 text-gray-900 placeholder-gray-400 transition duration-150 ease-in-out block w-full"
        v-model="model"
        ref="input"
    />
</template>
