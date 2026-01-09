<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';
import { notify } from "@kyvg/vue3-notification";
import TextArea from '@/components/TextArea.vue';
import TextInput from '@/components/TextInput.vue';
import InputCurrency from '@/components/InputCurrency.vue';
import { ref, onMounted } from 'vue';
import CategoryAccordion from '@/components/CategoryAccordion.vue';
import axios from 'axios';
import Select from '@/components/Select.vue';
import ImageUploader from '@/components/ImageUploader.vue';

const props = defineProps({
    product: Object
})

const form = useForm({
    title: props.product.title,
    description: props.product.description,
    price: props.product.price,
    stock_quantity: props.product.stock_quantity,
    mercado_livre_category_id: props.product.mercado_livre_category_id,
    mercado_livre_category_path: props.product.mercado_livre_category_path,
    attributes: props.product.attributes,
    listing_type: props.product.listing_type,
    pictures: []
});

const submit = () => {
    form.put(route('products.update', props.product.id), {
        onSuccess: () => {
            notify({
                type: 'success',
                title: 'Sucesso!',
                text: 'Produto atualizado com sucesso.'
            });
            form.reset();
        },
        onError: (error) => {
            if(error.message) {
                if(error.message.includes('<br>')) {
                    error.message.split('<br>').forEach((item) => {
                        notify({
                            type: 'error',
                            title: 'Erro!',
                            text: item
                        });
                    })
                } else {
                    notify({
                        type: 'error',
                        title: 'Erro!',
                        text: error.message
                    });
                }
            } else {
                for(let k in error) {
                    notify({
                        type: 'error',
                        title: 'Erro!',
                        text: error[k]
                    });
                }
            }
        }
    });
};

const listingTypes = ref([]);

onMounted(async () => {
    if(props.product.images.length > 0) {
        let pictures = [];
        props.product.images.forEach(img => {
            pictures.push(img.url);
        });
        form.pictures = pictures;
    }

    const { data: listingData } = await axios.get(route('mercado-livre.listing-types'));
    listingTypes.value = listingData;
});
</script>


<template>
    <Head title="Editar produto" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-400">Editar produto</h2>
        </template>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-zinc-900 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 mt-6">
                                    <InputLabel value="Imagens do Produto *" />

                                    <ImageUploader @update="form.pictures = $event" :initial-images="product.images"/>

                                    <p v-if="form.pictures.length == 0" class="text-xs text-gray-400 -mt-2">
                                        Selecione uma ou mais imagens para o produto.
                                    </p>

                                    <InputError :message="form.errors.pictures" />
                                </div>

                                <div class="col-span-12 sm:col-span-6">
                                    <InputLabel for="title" value="Nome Produto" />

                                    <TextInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.title"
                                        required
                                        placeholder="Nome do produto"
                                    />

                                    <InputError class="mt-2" :message="form.errors.title" />
                                </div>

                                <div class="col-span-12 sm:col-span-6">
                                    <InputLabel for="description" value="Descrição" />

                                    <TextArea
                                        id="description"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.description"
                                        required
                                        placeholder="Descreva o produto"
                                        rows="1"
                                    />

                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <div class="col-span-12 sm:col-span-4">
                                    <InputLabel for="price" value="Preço" />

                                    <InputCurrency
                                        id="price"
                                        class="mt-1 block w-full"
                                        v-model="form.price"
                                        required 
                                    />

                                    <InputError class="mt-2" :message="form.errors.price" />
                                </div>

                                <div class="col-span-12 sm:col-span-4">
                                    <InputLabel for="stock_quantity" value="Estoque Inicial" />

                                    <TextInput
                                        id="stock_quantity"
                                        type="number"
                                        class="mt-1 block w-full"
                                        step="1"
                                        v-model="form.stock_quantity"
                                        required
                                        placeholder="Quantidade inicial em estoque"
                                    />

                                    <InputError class="mt-2" :message="form.errors.stock_quantity" />
                                </div>

                                <div class="col-span-12 sm:col-span-4">
                                    <InputLabel for="listing_type" value="Tipo Anúncio" />

                                    <Select
                                        id="listing_type"
                                        class="mt-1 cursor-not-allowed"
                                        v-model="form.listing_type"
                                        disabled
                                        readonly
                                        :items="listingTypes" 
                                        value="id" 
                                        label="name" />

                                    <InputError class="mt-2" :message="form.errors.listing_type" />
                                </div>

                                <div class="col-span-12">
                                    <InputLabel value="Categoria" />

                                    <span v-for="(value, index) in product?.mercado_livre_category_path" :key="index" class="text-green-400 inline-block text-xl mt-3">
                                        <span v-if="index > 0" class="ms-1"> > </span> {{ value.name }}
                                    </span>

                                    <InputError :message="form.errors.mercado_livre_category_id" />
                                </div>

                                <div class="col-span-12">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div v-for="attr in product?.attributes" :key="attr.id" class="col-span-12 sm:col-span-4">
                                            <InputLabel :for="`att-${attr.id}`" :value="attr.label_name + ' *'" />

                                            <div class="w-full bg-white h-10 leading-10 px-2 rounded cursor-not-allowed mt-1">{{ attr.value_name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-between">
                                <Link :href="route('products.index')" class="text-gray-400">Voltar</Link>
                                <PrimaryButton
                                    class="ms-4 bg-[#8ebad9] hover:bg-[#8ebad9]/75"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    <Icon v-if="!form.processing" type="save" class="w-5 mr-2" />
                                    <Icon v-else type="clock" class="w-5 mr-2" />

                                    <span v-if="!form.processing">Salvar</span>
                                    <span v-else>Aguarde...</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>