<script setup>
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  categories: Array,
  path: {
    type: Array,
    default: () => []
  },
  selectedCategory: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['select', 'loading']);

const open = ref({});
const loading = ref({});
const children = ref({});

const onClickCategory = (category) => {
    const currentPath = [...props.path, category];

    if (children.value[category.id]) {
        if (children.value[category.id].length === 0) {
            emit('select', {
                category,
                path: currentPath
            });
        } else {
            open.value[category.id] = !open.value[category.id];
        }
        return;
    }

    loading.value[category.id] = true;
    emit('loading', true);

    axios.get(route('mercado-livre.categories', category.id)).then(({ data }) => {
        const childs = data.children_categories || [];
        children.value[category.id] = childs;
        
        if (childs.length === 0) {
            emit('select', {
                category,
                path: currentPath
            });
        } else {
            open.value[category.id] = true;
        }
    }).finally(() => {
        emit('loading', false);
        loading.value[category.id] = false;
    });
}
</script>

<template>
    <ul class="space-y-2">
        <li v-for="cat in categories" :key="cat.id">
            <button type="button" @click="onClickCategory(cat)" class="w-full text-left cursor-pointer" :class="{
                'text-gray-300 hover:text-white': selectedCategory?.id !== cat.id,
                'text-green-400 font-semibold': selectedCategory?.id === cat.id
            }">
                {{ cat.name }} <img v-if="loading[cat.id]" class="inline-block w-5" src="/loading.svg" alt="">
            </button>

            <div v-if="open[cat.id]" class="ml-4 mt-2 border-l border-gray-700 pl-4">
                <CategoryAccordion
                    :categories="children[cat.id]"
                    :path="[...path, cat]"
                    @select="emit('select', $event)"
                    @loading="emit('loading', $event)"
                    :selected-category="selectedCategory"
                />
            </div>
        </li>
    </ul>
</template>
