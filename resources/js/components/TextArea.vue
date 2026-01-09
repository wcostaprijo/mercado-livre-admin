<script setup>
const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <textarea v-model="model" class="rounded-md border-gray-100 py-2 px-3 shadow-sm focus:ring-2 bg-white outline-none focus:outline-none focus:border-0 focus:ring-[#8726d5]"></textarea>
</template>
