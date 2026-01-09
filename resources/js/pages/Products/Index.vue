<script setup>
import Icon from '@/components/Icon.vue';
import Table from '@/components/Table.vue';
import TableTd from '@/components/table/TableTd.vue';
import TableTh from '@/components/table/TableTh.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { notify } from '@kyvg/vue3-notification';

const props = defineProps({
    items: Object,
    search: String,
});

const form = useForm({
    search: props.search || ''
});

const searchTable = (text) => {
    form.search = text;
    form.get(route('products.index'));
};

const onNextPage = (url) => {
    let data = {};
    if(props.search) {
        data.search = props.search;
    }
    useForm(data).get(url);
};

const toReal = (value) => {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};
</script>

<template>
    <Head title="Produtos" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-400">Produtos</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-zinc-900 shadow-sm sm:rounded-lg">
                    <div class="px-6 py-8 text-gray-900">
                        <div class="text-end">
                            <Link :href="route('products.create')" class="bg-[#8726d5] hover:bg-[#8726d5]/75 py-2 px-4 text-white rounded-lg">
                                <Icon type="add" class="w-5 inline"/>
                                Cadastrar
                            </Link>
                        </div>

                        <Table v-model="form.search" @on-search="searchTable" @on-next-page="onNextPage" :links="items.links">
                            <template #title>Produtos cadastrados</template>
                            <template #thead>
                                <tr>
                                    <TableTh class="table-cell sm:hidden text-center">Registros</TableTh>
                                    <TableTh class="hidden sm:table-cell"></TableTh>
                                    <TableTh class="hidden sm:table-cell">Nome</TableTh>
                                    <TableTh class="hidden sm:table-cell">Status</TableTh>
                                    <TableTh class="hidden sm:table-cell">Descrição</TableTh>
                                    <TableTh class="hidden sm:table-cell">Preço</TableTh>
                                    <TableTh class="hidden sm:table-cell">Estoque</TableTh>
                                    <TableTh class="hidden sm:table-cell"></TableTh>
                                </tr>
                            </template>
                            <template #tbody>
                                <tr v-for="item in items.data" :key="item.id" class="odd:bg-[#F5EBF2] even:bg-[#F3E0FF]">
                                    <TableTd class="table-cell sm:hidden">
                                        <img :src="item?.images?.[0]?.url || '/sem-foto.jpeg'" class="float-left block h-20 w-20 rounded-full ring-2 ring-purple-700 me-4" alt="">
                                        <p class="mt-2 text-lg">{{ item.title }}</p>
                                        <div class="clear-both"></div>
                                        <div class="mt-3 text-lg">
                                            <small class="-mb-1 block font-bold">Status</small>
                                            <p>{{ item.status_label }}</p>
                                        </div>
                                        <div class="mt-3 text-lg">
                                            <small class="-mb-1 block font-bold">Descrição</small>
                                            <p class="capitalize">{{ item.description }}</p>
                                        </div>
                                        <div class="flex justify-between">
                                            <div class="mt-3 text-lg">
                                                <small class="-mb-1 block font-bold">Preço</small>
                                                <p class="capitalize">{{ toReal(item.price) }}</p>
                                            </div>
                                            <div class="mt-3 text-lg">
                                                <small class="-mb-1 block font-bold">Estoque</small>
                                                <p class="capitalize">{{ item.stock_quantity }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <Link :href="route('products.edit', item.id)" class="block mt-4 bg-purple-400 text-white text-center px-4 py-2 rounded-lg cursor-pointer">
                                                <Icon type="edit" class="w-5 inline-block -mt-1 me-3" />
                                                <p class="inline-block">Editar Produto</p>
                                            </Link>
                                            <a  v-if="item.product_url" :href="item.product_url" target="_blank" class="block mt-2 bg-yellow-500 text-white text-center px-4 py-2 rounded-lg cursor-pointer">
                                                <Icon type="eye" class="w-5 inline-block -mt-1 me-3" />
                                                <p class="inline-block">Ver produto no Mercado Livre</p>
                                            </a>
                                        </div>
                                    </TableTd>
                                    <TableTd class="hidden sm:table-cell">
                                        <img :src="item?.images?.[0]?.url || '/sem-foto.jpeg'" class="inline-block h-12 w-12 rounded-full ring-2 ring-purple-700 object-cover" alt="">
                                    </TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ item.title }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ item.status_label }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ item.description }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ toReal(item.price) }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ item.stock_quantity }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">
                                        <div class="flex justify-end">
                                            <div class="relative group">
                                                <Link :href="route('products.edit', item.id)" class="block cursor-pointer">
                                                    <Icon type="edit" class="w-5" />
                                                </Link>
                                                <div class="max-w-xs absolute shadow-lg hidden group-hover:block bg-green-500 text-white font-semibold px-4 py-2 text-[13px] right-full mr-3 top-0 bottom-0 my-auto h-max w-max rounded before:w-4 before:h-4 before:rotate-45 before:bg-green-500 before:absolute before:z-[0] before:bottom-0 before:top-0 before:my-auto before:-right-1">
                                                    Editar Produto
                                                </div>
                                            </div>
                                            <div v-if="item.product_url" class="relative group ms-2">
                                                <a :href="item.product_url" class="block cursor-pointer" target="_blank">
                                                    <Icon type="eye" class="w-5" />
                                                </a>
                                                <div class="max-w-xs absolute shadow-lg hidden group-hover:block bg-green-500 text-white font-semibold px-4 py-2 text-[13px] right-full mr-3 top-0 bottom-0 my-auto h-max w-max rounded before:w-4 before:h-4 before:rotate-45 before:bg-green-500 before:absolute before:z-[0] before:bottom-0 before:top-0 before:my-auto before:-right-1">
                                                    Ver produto no Mercado Livre
                                                </div>
                                            </div>
                                        </div>
                                    </TableTd>
                                </tr>
                                <tr v-if="items.data.length == 0" class="odd:bg-[#F5EBF2] even:bg-[#F3E2EF]">
                                    <TableTd colspan="7" class="text-center">Nenhum registro foi encontrado.</TableTd>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
