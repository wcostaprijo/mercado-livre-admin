<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <AuthLayout>
        <Head title="Recuperar Senha" />

        <div>
            <div class="mb-5 sm:mb-8">
                <h1 class="text-3xl sm:text-title-md mb-1 font-semibold text-white/90">
                    Recuperar Senha
                </h1>
                <p class="text-sm text-gray-400">
                    Esqueceu sua senha? Sem problemas. Basta nos informar seu endereço de e-mail
                    e enviaremos um link para redefinição de senha que permitirá você escolher uma nova.
                </p>
            </div>
            <div>
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

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

                    <div class="mt-10 flex items-center justify-between">
                        <Link :href="route('login')" class="rounded-md text-sm text-gray-400 underline hover:text-[#8726d5] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Voltar ao Login
                        </Link>

                        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Recuperar Senha
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
