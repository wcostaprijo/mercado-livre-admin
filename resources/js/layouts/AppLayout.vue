<script setup>
import { ref, computed, watch } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import Dropdown from '@/components/Dropdown.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import NavLink from '@/components/NavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '@/components/Icon.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();

const isRouteActive = (routeName) => {
  return page.url.startsWith(`/${routeName}`) || route().current(routeName);
};
</script>

<template>
    <div class="flex min-h-screen bg-[#0f070f]">
        <notifications position="top right" class="mt-2" />
        <div class="w-64 lg:block shadow border-r border-zinc-800 bg-zinc-900" :class="{
            'fixed h-full': showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
        }">
            <div class="py-2 px-6 flex items-center justify-center">
                <Link :href="route('dashboard')">
                    <AppLogo class="w-full" />
                </Link>
            </div>

            <div class="mb-10 mt-5 px-2">
                <NavLink :href="route('dashboard')" :active="isRouteActive('dashboard')" class="ps-4 !opacity-100 mb-1">
                    <Icon type="home" class="h-5 w-5 text-gray-400 mr-2"/>
                    Início
                </NavLink>

                <NavLink :href="route('products.index')" :active="isRouteActive('products')" class="ps-4 !opacity-100 mb-1">
                    <Icon type="products" class="h-5 w-5 text-gray-400 mr-2"/>
                    Produtos
                </NavLink>

                <NavLink :href="route('orders.index')" :active="isRouteActive('orders')" class="ps-4 !opacity-100 mb-1">
                    <Icon type="list" class="h-5 w-5 text-gray-400 mr-2"/>
                    Pedidos
                </NavLink>

                <NavLink :href="route('logout')" method="post" as="button" class="ps-4 w-full cursor-pointer">
                    <Icon type="log-out" class="h-5 w-5 text-gray-400 mr-2"/>
                    Sair
                </NavLink>
            </div>
        </div>

        <div class="flex-1">
            <div class="flex justify-between py-3 px-6 space-x-6 bg-zinc-900 border-b border-zinc-800">
                <div class="py-1 px-0">
                    <div v-if="$slots.header" class="mx-auto max-w-7xl px-4 py-0 lg:px-6 lg:px-8 hidden lg:block">
                        <slot name="header" />
                    </div>

                    <Link :href="route('dashboard')" class="lg:hidden" :class="{
                        block: !showingNavigationDropdown,
                        hidden: showingNavigationDropdown,
                    }">
                        <AppLogo class="h-8" />
                    </Link>
                </div>
                
                <div class="hidden lg:ms-6 lg:flex lg:items-center">
                    <div class="relative ms-3 flex items-center text-gray-400">
                        <Icon type="person" class="h-5 w-5 fill-gray-400 me-1 mt-2" />
                        {{ $page.props.auth.user.name }}
                    </div>
                </div>

                <div class="-me-2 flex items-center lg:hidden">
                    <button
                        @click="showingNavigationDropdown = !showingNavigationDropdown"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out bg-gray-750 text-white bg-gray-750 text-white outline-none"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex':
                                        !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex':
                                        showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.rotate-90 {
  transform: rotate(90deg);
}
</style>
