<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { notify } from '@kyvg/vue3-notification';

const props = defineProps({
    connected: Boolean,
    authorization_url: String
});

const page = usePage();

onMounted(() => {
    if (page.props.flash.success) {
        notify({
            type: 'success',
            title: 'Sucesso!',
            text: page.props.flash.success
        });
    }

    if (page.props.flash.error) {
        notify({
            type: 'error',
            title: 'Erro!',
            text: page.props.flash.error
        });
    }
});

</script>

<template>
    <Head title="Início" />

    <AppLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-400">Início</h2>
        </template>
        
        <div class="py-8">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-zinc-900 shadow-sm sm:rounded-lg">
                    <div class="px-6 py-8 text-gray-400">
                        <div class="text-xl">Olá, <strong>{{ $page.props.auth.user.name }}</strong>!</div>
                        <p>Para começar a usar o sistema, certifique-se de estar conectado ao Mercado Livre, para que seja possível gerenciar seus produtos e pedidos.</p>
                    
                        <div class="mt-10">
                            <p>Status da conexão:</p>
                            <p v-if="connected" class="py-3 px-6 bg-green-500 inline-block mt-3 rounded-full text-white font-bold">
                                Conectado ao Mercado Livre
                            </p>
                            <p v-else class="py-3 px-6 bg-red-500 inline-block mt-3 rounded-full text-white font-bold">
                                Desconectado
                            </p>
                            <div class="mt-5">
                                <a v-if="!connected" :href="authorization_url" class="text-blue-400 hover:underline">Clique aqui e conecte-se ao Mercado Livre</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
