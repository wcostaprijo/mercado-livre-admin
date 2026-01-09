<script setup>
import AppLogo from '@/components/AppLogo.vue';
</script>

<template>
    <div class="relative z-1 p-0 bg-gray-600">
        <notifications position="top right" class="mt-2" />
        <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row bg-zinc-800">
            <div class="flex w-full flex-1 flex-col lg:w-1/2">
                <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center p-6 sm:p-0">
                    <div class="mb-0 block sm:hidden">
                        <AppLogo class="w-full mx-auto" />
                    </div>

                    <slot />
                </div>
            </div>

            <div class="relative hidden h-full w-full items-center lg:grid lg:w-1/2 bg-[#0f070f]">
                <div class="z-1 flex items-center justify-center">
                    <div class="flex max-w-xs flex-col items-center">
                        <AppLogo class="h-full" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
