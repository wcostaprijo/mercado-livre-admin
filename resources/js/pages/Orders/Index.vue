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
    form.get(route('orders.index'));
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
    <Head title="Pedidos" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-400">Pedidos</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-zinc-900 shadow-sm sm:rounded-lg">
                    <div class="px-6 py-8 text-gray-900">
                        <Table v-model="form.search" @on-search="searchTable" @on-next-page="onNextPage" :links="items.links">
                            <template #title>Lista de pedidos</template>
                            <template #thead>
                                <tr>
                                    <TableTh class="table-cell sm:hidden text-center">Registros</TableTh>
                                    <TableTh class="hidden sm:table-cell">Comprador</TableTh>
                                    <TableTh class="hidden sm:table-cell">Produtos</TableTh>
                                    <TableTh class="hidden sm:table-cell">Total</TableTh>
                                    <TableTh class="hidden sm:table-cell">Status</TableTh>
                                    <TableTh class="hidden sm:table-cell"></TableTh>
                                </tr>
                            </template>
                            <template #tbody>
                                <tr v-for="order in items.data" :key="order.id" class="odd:bg-[#F5EBF2] even:bg-[#F3E0FF]">
                                    <TableTd class="table-cell sm:hidden">
                                        <div class="mt-3 text-lg">
                                            <small class="-mb-1 block font-bold">Comprador</small>
                                            <p class="capitalize">{{ order.buyer_name }}</p>
                                        </div>
                                        <div class="mt-3 text-lg">
                                            <small class="-mb-1 block font-bold">Status</small>
                                            <p>{{ order.status_label }}</p>
                                        </div>
                                        <div class="mt-3 text-lg">
                                            <small class="-mb-1 block font-bold">Produtos</small>
                                            <p v-for="(item, index) in order.items" :key="index" class="max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">
                                                {{ item.quantity }}x {{ item.title }}
                                            </p>
                                        </div>
                                        <div class="mt-3 text-lg">
                                            <small class="-mb-1 block font-bold">Total</small>
                                            <p class="capitalize">{{ toReal(order.total_amount) }}</p>
                                        </div>
                                        <div class="mt-3">
                                            <Link :href="route('orders.show', order.id)" class="block mt-4 bg-purple-400 text-white text-center px-4 py-2 rounded-lg cursor-pointer">
                                                <Icon type="eye" class="w-5 inline-block -mt-1 me-3" />
                                                <p class="inline-block">Ver Pedido</p>
                                            </Link>
                                        </div>
                                    </TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ order.buyer_name }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">
                                        <p v-for="(item, index) in order.items" :key="index" class="max-w-[200px] overflow-hidden text-ellipsis whitespace-nowrap">
                                            {{ item.quantity }}x {{ item.title }}
                                        </p>
                                    </TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ toReal(order.total_amount) }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">{{ order.status_label }}</TableTd>
                                    <TableTd class="hidden sm:table-cell">
                                        <div class="flex justify-end">
                                            <div class="relative group">
                                                <Link :href="route('orders.show', order.id)" class="block cursor-pointer">
                                                    <Icon type="eye" class="w-5" />
                                                </Link>
                                                <div class="max-w-xs absolute shadow-lg hidden group-hover:block bg-green-500 text-white font-semibold px-4 py-2 text-[13px] right-full mr-3 top-0 bottom-0 my-auto h-max w-max rounded before:w-4 before:h-4 before:rotate-45 before:bg-green-500 before:absolute before:z-[0] before:bottom-0 before:top-0 before:my-auto before:-right-1">
                                                    Ver Pedido
                                                </div>
                                            </div>
                                        </div>
                                    </TableTd>
                                </tr>
                                <tr v-if="items.data.length == 0" class="odd:bg-[#F5EBF2] even:bg-[#F3E2EF]">
                                    <TableTd colspan="5" class="text-center">Nenhum registro foi encontrado.</TableTd>
                                </tr>
                            </template>
                        </Table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
