<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    order: Object
});

const toReal = (value) => {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};
</script>

<template>
    <Head title="Detalhes do pedido" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-400">Detalhes do pedido</h2>
        </template>
        
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-zinc-900 shadow-sm sm:rounded-lg">
                    <div class="px-6 py-8 text-gray-400">
                        <div class="border-b border-gray-300 pb-4">
                            <div class="sm:flex justify-between">
                                <h3 class="text-2xl font-semibold text-gray-400">Pedido #{{ order.mercado_livre_id }}</h3>
                                <Link :href="route('orders.index')" class="inline-flex mt-3 sm:mt-0 items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 cursor-pointer">
                                    Voltar para lista de pedidos
                                </Link>
                            </div>
                            <div class="flex justify-between max-sm:flex-col gap-6 mt-4">
                                <div class="mt-4">
                                    <p class="text-gray-400 text-sm font-medium">Comprador:</p>
                                    <p class="text-gray-400 text-lg mt-0 font-medium">{{ order.buyer_name }}</p>
                                </div>

                                <div class="mt-4 sm:text-end">
                                    <p class="text-gray-400 text-sm font-medium">Status:</p>
                                    <p class="text-gray-400 text-lg mt-0 font-medium">{{ order.status_label }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-for="(item, index) in order.items" :key="index">
                            <div class="divide-y divide-gray-300">
                                <div class="grid grid-cols-4 max-sm:grid-cols-2 items-start justify-between gap-6 py-4">
                                    <div class="col-span-2 flex sm:items-center gap-4 sm:gap-6 max-sm:flex-col">
                                        <div class="bg-gray-100 p-2 rounded-lg w-24 h-24 shrink-0 hidden sm:block">
                                            <img :src="item?.product?.images?.[0]?.url || '/sem-foto.jpeg'" class="w-full h-full object-contain" alt="">
                                        </div>
                                        <div>
                                            <h6 v-if="!item?.product" class="text-sm font-semibold text-gray-400">{{ item?.title }}</h6>
                                            <a v-else :href="item.product?.product_url" target="_blank" class="text-sm font-semibold text-blue-400 hover:underline">
                                                {{ item?.product?.title }}
                                            </a>
                                        </div>
                                    </div>
    
                                    <div class="sm:text-center mt-3">
                                        <h6 class="text-sm font-semibold text-gray-400">Quantidade</h6>
                                        <p class="sm:text-xl font-medium mt-2 text-gray-400">
                                            {{item.quantity}}
                                        </p>
                                    </div>
    
                                    <div class="ml-auto mt-3 text-end">
                                        <h6 class="text-sm font-semibold text-gray-400">Preço</h6>
                                        <p class="text-sm text-gray-400 font-medium mt-2">{{ toReal(item.unit_price) }}</p>
                                    </div>
                                </div>
                            </div>
    
                            <hr class="border-t border-gray-300" />
                        </div>

                        <div class="max-w-md ml-auto divide-y divide-gray-300 mt-4">
                            <div class="flex justify-between font-semibold text-[15px] text-gray-400 py-4">
                                <span>Total</span>
                                <span>{{ toReal(order.total_amount) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
