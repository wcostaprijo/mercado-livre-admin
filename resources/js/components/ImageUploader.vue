<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Icon from '@/components/Icon.vue';

const props = defineProps({
    initialImages: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['update']);

const images = ref(props.initialImages);
const uploading = ref(false);

const onSelectFiles = async (e) => {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    uploading.value = true;

    const formData = new FormData();
    files.forEach(file => formData.append('images[]', file));

    const { data } = await axios.post(
        route('products.upload-images'),
        formData,
        { headers: { 'Content-Type': 'multipart/form-data' } }
    );

    images.value.push(...data);
    emit('update', images.value.map(img => img.url));

    uploading.value = false;
};

const removeImage = (index) => {
    images.value.splice(index, 1);
    emit('update', images.value.map(img => img.url));
};
</script>

<template>
    <div class="space-y-3">
        <label class="block cursor-pointer">
            <input
                type="file"
                multiple
                accept="image/*"
                class="hidden"
                @change="onSelectFiles"
            />
            <div class="flex items-center gap-2 text-sm text-blue-400 hover:text-blue-300">
                <Icon type="image" class="w-4" />
                <span>Adicionar imagens</span>
            </div>
        </label>

        <div v-if="uploading" class="text-sm text-gray-400 mb-5">
            Carregando imagens <img src="/loading.svg" class="w-5 inline-block">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-6 gap-3">
            <div v-for="(img, index) in images" :key="img.url" class="relative group bg-zinc-700/60 p-2 rounded">
                <img :src="img.url" class="h-36 rounded mx-auto"/>

                <button type="button" @click="removeImage(index)" class="absolute top-1 right-1 bg-black/70 text-white text-xs px-1 rounded sm:opacity-0 sm:group-hover:opacity-100 cursor-pointer">
                    ✕
                </button>
            </div>
        </div>
    </div>
</template>

