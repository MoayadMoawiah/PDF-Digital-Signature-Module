<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();
</script>

<template>
    <Head :title="t('rejected.title')" />
    <div class="min-h-screen bg-surface flex flex-col items-center justify-center p-6">
        <div class="absolute top-4 end-4">
            <LanguageSwitcher />
        </div>
        <div class="bg-white rounded-2xl shadow-card border border-slate-200 p-10 max-w-md w-full text-center">
            <div class="w-16 h-16 bg-danger/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-danger" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-slate-800 mb-3">{{ t('rejected.title') }}</h1>
            <p class="text-slate-600 mb-2">{{ t('rejected.message') }}</p>
            <p class="text-sm text-slate-400">{{ t('rejected.sub') }}</p>
        </div>
    </div>
</template>
