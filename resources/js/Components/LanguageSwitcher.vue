<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { locale } = useI18n();

const current = computed(() => locale.value);

function switchTo(lang) {
    locale.value = lang;
    localStorage.setItem('locale', lang);
    document.documentElement.lang = lang;
    document.documentElement.dir  = lang === 'ar' ? 'rtl' : 'ltr';
}
</script>

<template>
    <div class="flex items-center gap-0.5 bg-slate-100 rounded-lg p-1">
        <button @click="switchTo('ar')"
                :class="[
                    'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                    current === 'ar'
                        ? 'bg-white text-primary shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'
                ]">
            عربي
        </button>
        <button @click="switchTo('en')"
                :class="[
                    'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                    current === 'en'
                        ? 'bg-white text-primary shadow-sm'
                        : 'text-slate-500 hover:text-slate-700'
                ]">
            EN
        </button>
    </div>
</template>
