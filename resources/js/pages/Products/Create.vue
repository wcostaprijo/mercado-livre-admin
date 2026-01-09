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

const form = useForm({
    title: '',
    description: '',
    price: 0,
    stock_quantity: '0',
    mercado_livre_category_id: null,
    mercado_livre_category_path: [],
    attributes: [],
    listing_type: '',
    pictures: []
});

const categoryAttributes = ref([]);
const loadingCategories = ref(false);
const loadingAttributes = ref(false);

const loadAttributes = async (categoryId) => {
    loadingAttributes.value = true;
    categoryAttributes.value = [];
    form.attributes = [];

    const { data } = await axios.get(
        route('mercado-livre.categories.attributes', categoryId)
    );

    categoryAttributes.value = data.filter(attr =>
        !attr.tags?.hidden &&
        !attr.tags?.read_only
    );

    loadingAttributes.value = false;
};

const onAttributeChange = (attr, payload) => {
    const oldAttribute = form.attributes.find(a => a.id === attr.id);
    const index = form.attributes.findIndex(a => a.id === attr.id);

    if (payload === null || payload === '') {
        if (index !== -1) form.attributes.splice(index, 1);
        return;
    }

    const attribute = { id: attr.id, label_name: attr.name };

    if (attr.value_type === 'list') {
        attribute.value_id = payload.id;
        attribute.value_name = payload.name;
    }

    else if (attr.value_type === 'boolean') {
        attribute.value_name = payload.name;
        attribute.value = payload.metadata.value;
    }

    else if (attr.value_type === 'number_unit') {
        if(payload.value.length > 0) {
            attribute.value_name = payload.value;
        } else {
            attribute.value_name = oldAttribute ? oldAttribute.value_name : '';
        }

        if(oldAttribute && payload.value.length > 0 && oldAttribute.unit) {
            attribute.unit = oldAttribute.unit;
        } else {
            attribute.unit = payload.unit;
        }
    }

    else {
        attribute.value_name = payload;
    }

    if (index === -1) {
        form.attributes.push(attribute);
    } else {
        form.attributes[index] = attribute;
    }
};

const categories = ref([]);
const selectedCategory = ref(null);
const categoryPath = ref([]);

const onCategorySelect = async ({ category, path }) => {
    selectedCategory.value = category;
    categoryPath.value = path;

    form.mercado_livre_category_id = category.id;
    form.mercado_livre_category_path = path.map(c => ({
        id: c.id,
        name: c.name
    }));

    await loadAttributes(category.id);
};

const submit = () => {
    form.post(route('products.store'), {
        onSuccess: () => {
            notify({
                type: 'success',
                title: 'Sucesso!',
                text: 'Produto cadastrado com sucesso.'
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

const onLoadingCategories = (isLoading) => {
    loadingCategories.value = isLoading;
};

const listingTypes = ref([]);

onMounted(async () => {
    const { data } = await axios.get(route('mercado-livre.categories'));
    categories.value = data;

    const { data: listingData } = await axios.get(route('mercado-livre.listing-types'));
    listingTypes.value = listingData;
});
</script>


<template>
    <Head title="Cadastrar produto" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-400">Cadastrar produto</h2>
        </template>
        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-zinc-900 shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-12 gap-4">
                                <div class="col-span-12 mt-6">
                                    <InputLabel value="Imagens do Produto *" />

                                    <ImageUploader
                                        @update="form.pictures = $event"
                                    />

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
                                        class="mt-1"
                                        v-model="form.listing_type"
                                        required
                                        :items="listingTypes" 
                                        value="id" 
                                        label="name" />

                                    <InputError class="mt-2" :message="form.errors.listing_type" />
                                </div>

                                <div class="col-span-12">
                                    <InputLabel value="Categoria" />

                                    <div class="mt-2 rounded bg-zinc-800 p-4 max-h-96 overflow-y-auto">
                                        <CategoryAccordion :categories="categories" @select="onCategorySelect" :selected-category="selectedCategory" @loading="onLoadingCategories"/>
                                    </div>

                                    <InputError :message="form.errors.mercado_livre_category_id" />
                                </div>

                                <div v-if="categoryAttributes.length && !loadingAttributes" class="col-span-12">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div v-for="attr in categoryAttributes" :key="attr.id" :class="{
                                            'col-span-12 sm:col-span-4': attr?.tags?.required || attr?.required || attr?.tags?.catalog_required,
                                            'hidden': !attr?.tags?.required && !attr?.required && !attr?.tags?.catalog_required
                                        }">
                                            <div v-if="attr?.tags?.required || attr?.required || attr?.tags?.catalog_required">
                                                <InputLabel :for="`att-${attr.id}`" :value="attr.name + ' *'" />

                                                <Select v-if="['list', 'boolean'].includes(attr.value_type)"
                                                    :id="`att-${attr.id}`"
                                                    class="mt-1"
                                                    required
                                                    @change="onAttributeChange(attr, attr.values.find(v => v.id === $event.target.value))"
                                                    :items="attr.values" value="id" label="name" />
    
                                                <div v-else-if="attr.value_type === 'number_unit'" class="flex gap-2">
                                                    <TextInput
                                                        type="number"
                                                        class="mt-1 block w-full"
                                                        required
                                                        @input="onAttributeChange(attr, {
                                                            value: $event.target.value,
                                                            unit: attr.default_unit
                                                        })"
                                                    />
    
                                                    <select 
                                                        class="mt-1 rounded text-dark bg-white outline-none" 
                                                        @change="onAttributeChange(attr, { value: '', unit: $event.target.value})" 
                                                        required
                                                    >
                                                        <option
                                                            v-for="u in attr.allowed_units"
                                                            :key="u.id"
                                                            :value="u.id"
                                                        >
                                                            {{ u.name }}
                                                        </option>
                                                    </select>
                                                </div>
    
                                                <TextInput
                                                    v-else
                                                    class="mt-1 block w-full"
                                                    required
                                                    @input="onAttributeChange(attr, $event.target.value)"
                                                />
    
                                                <p v-if="attr.hint" class="text-xs text-gray-400 mt-1">
                                                    {{ attr.hint }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex items-center justify-between">
                                <Link :href="route('products.index')" class="text-gray-400">Voltar</Link>
                                <PrimaryButton
                                    class="ms-4 bg-[#8ebad9] hover:bg-[#8ebad9]/75"
                                    :class="{ 'opacity-25': form.processing || loadingCategories || loadingAttributes }"
                                    :disabled="form.processing || loadingCategories || loadingAttributes"
                                >
                                    <Icon v-if="!form.processing && !loadingCategories && !loadingAttributes" type="add" class="w-5 mr-2" />
                                    <Icon v-else type="clock" class="w-5 mr-2" />

                                    <span v-if="!form.processing && !loadingCategories && !loadingAttributes">Cadastrar</span>
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