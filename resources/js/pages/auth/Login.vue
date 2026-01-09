<script setup>
import Checkbox from '@/components/Checkbox.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/components/SecondaryButton.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Acessar Sistema" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div>
            <div class="mb-5 sm:mb-8">
                <h1 class="text-3xl sm:text-title-md mb-2 font-semibold text-white/90">
                    Acessar Sistema
                </h1>
                <p class="text-sm text-gray-400">
                    Insira seu e-mail e senha para entrar!
                </p>
            </div>
            <div>
                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="email" value="E-mail" class="text-gray-400" />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="email@exemplo.com"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <div class="flex justify-between">
                            <InputLabel for="password" value="Senha" class="text-gray-400" />

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="rounded-md text-sm text-gray-400 underline hover:text-[#8726d5] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Esqueceu sua senha?
                            </Link>
                        </div>

                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="******"
                        />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="mt-4 block">
                        <label class="flex items-center cursor-pointer">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-sm text-gray-400">Mantenha-me conectado</span
                            >
                        </label>
                    </div>

                    <div class="mt-10 flex items-center justify-between">
                        <Link :href="route('register')" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 cursor-pointer">
                            Criar Conta
                        </Link>

                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Entrar
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
