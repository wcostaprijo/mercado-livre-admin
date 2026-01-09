<script setup>
const props = defineProps({
    bg: {
        type: String,
        required: false,
        default: ''
    }
});
</script>

<template>
    <td :class="`px-5 py-5 border-b border-[#E3D3DF] text-sm ${bg}`">
        <slot />
    </td>
</template>