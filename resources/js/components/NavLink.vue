<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center px-2 py-2.5 text-white hover:text-white group bg-[#8726d5]/25 rounded-lg'
        : 'flex items-center px-2 py-2.5 text-gray-400 hover:bg-[#8726d5]/25 rounded-lg group',
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
