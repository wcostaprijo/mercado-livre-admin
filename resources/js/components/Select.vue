<script setup>

defineProps({
    items: Array,
    value: {
        type: String,
        default: 'value',
        required: false
    },
    label: {
        type: String,
        default: 'label',
        required: false
    }
});

const model = defineModel({
    type: String,
    required: true,
});
</script>

<template>
    <select v-model="model" class="rounded-md bg-white border-gray-300 shadow-sm focus:border-0 focus:ring-0 outline-none w-full h-10 px-2">
        <option v-for="(item, index) in items" :key="index" :value="item[value]">
            {{ item[label] }}
        </option>
    </select>
</template>