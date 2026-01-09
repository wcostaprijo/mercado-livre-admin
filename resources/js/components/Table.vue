<script setup>
import TextInput from '@/components/TextInput.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import '../../css/app.css';

const props = defineProps({
    modelValue: String,
    links: {
        type: Array,
        default: []
    },
    with_title: {
        type: Boolean,
        required: false,
        default: true
    },
    with_search: {
        type: Boolean,
        required: false,
        default: true
    },
    classes_px: {
        type: String,
        required: false,
        default: 'px-4 sm:px-8'
    },
    with_margin_top: {
        type: Boolean,
        required: false,
        default: true
    },
});

const emit = defineEmits(['update:modelValue', 'onSearch', 'onNextPage']);
const search = ref(props.modelValue || '');

const delayedSearch = debounce(() => {
    emit('onSearch', search.value);
}, 500);

const updateSearch = (value) => {
    search.value = value;
    emit('update:modelValue', value);
    delayedSearch();
};

watch(search, (newValue) => {
    emit('update:modelValue', newValue);
});
</script>

<template>
    <div :class="{'container mx-auto': true, classes_px: with_margin_top}">
        <div :class="{'py-8': with_margin_top}">
            <div class="grid grid-cols-1 sm:grid-cols-3">
                <h2 v-if="with_title" class="mb-3 sm:mb-0 text-2xl leading-tight text-gray-400" :class="{'sm:col-span-2': with_search, 'sm:col-span-3': !with_search}">
                    <slot name="title" />
                </h2>
                <div v-if="with_search" class="text-end">
                    <TextInput
                        type="text"
                        class="mt-1 w-full"
                        autocomplete="search"
                        placeholder="Pesquisar..."
                        v-model="search"
                        @keyup="updateSearch(search)"
                        autofocus
                    />
                </div>
            </div>
            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 overflow-x-auto" :class="{'py-8': with_margin_top}">
                <div class="inline-block min-w-full shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full leading-normal striped-table">
                        <thead >
                            <slot name="thead" />
                        </thead>
                        <tbody>
                            <slot name="tbody" />
                        </tbody>
                        <tfoot>
                            <slot name="tfoot" />
                        </tfoot>
                    </table>
                </div>
            </div>
            <div v-if="links.length > 0">
                <ul class="flex space-x-4 justify-center mt-1 font-[sans-serif]">
                    <li v-for="(link, index) in links" :key="index" class="cursor-pointer flex items-center justify-center shrink-0 w-4 sm:w-9 h-6 sm:h-9 rounded-md" @click="emit('onNextPage', link.url)" :class="{'bg-white text-dark': link.active, 'text-gray-400': !link.active}">
                        <span v-html="link.label"></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>