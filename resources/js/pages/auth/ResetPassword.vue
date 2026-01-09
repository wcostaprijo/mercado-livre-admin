<script setup>
import AuthLayout from '@/layouts/AuthLayout.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthLayout>
        <Head title="Nova Senha" />
        <div>
            <div class="mb-5 sm:mb-8">
                <h1 class="text-3xl sm:text-title-md mb-1 font-semibold text-white/90">
                    Nova Senha
                </h1>
                <p class="text-sm text-gray-400">
                    Defina uma nova senha para sua conta preenchendo os campos abaixo.
                </p>
            </div>
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
                    <InputLabel for="password" value="Nova Senha" class="text-gray-400" />
    
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        placeholder="******"
                    />
    
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
    
                <div class="mt-4">
                    <InputLabel for="password_confirmation" value="Confirmar Nova Senha" class="text-gray-400"/>
    
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="******"
                    />
    
                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>
    
                <div class="mt-4 flex items-center justify-end">
                    <Link :href="route('login')" class="rounded-md text-sm text-gray-400 underline hover:text-[#8726d5] focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancelar
                    </Link>
                    <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Salvar Senha
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthLayout>
</template>
