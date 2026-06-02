<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t }    = useI18n();
const page     = usePage();
const mobileOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-surface">
        <!-- Nav -->
        <nav class="bg-primary shadow-sm sticky top-0 z-30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo + links -->
                    <div class="flex items-center gap-8">
                        <Link :href="route('admin.documents.index')"
                              class="text-white font-bold text-lg tracking-tight flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z" />
                            </svg>
                            {{ t('app.name') }}
                        </Link>
                        <div class="hidden sm:flex items-center gap-1">
                            <Link :href="route('admin.documents.index')"
                                  class="px-3 py-1.5 rounded-md text-sm font-medium text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                                {{ t('nav.documents') }}
                            </Link>
                        </div>
                    </div>

                    <!-- Right side -->
                    <div class="flex items-center gap-4">
                        <LanguageSwitcher />
                        <div class="hidden sm:flex items-center gap-3 text-white/70 text-sm">
                            <span>{{ page.props.auth?.user?.name }}</span>
                            <Link :href="route('logout')" method="post" as="button"
                                  class="hover:text-white transition-colors">
                                {{ t('nav.logout') }}
                            </Link>
                        </div>
                        <!-- Mobile menu toggle -->
                        <button @click="mobileOpen = !mobileOpen"
                                class="sm:hidden text-white/80 hover:text-white p-1">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path v-if="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile menu -->
            <div v-if="mobileOpen" class="sm:hidden bg-primary-dark border-t border-white/10 px-4 py-3 space-y-2">
                <Link :href="route('admin.documents.index')" class="block text-sm text-white/80 hover:text-white py-1">
                    {{ t('nav.documents') }}
                </Link>
                <Link :href="route('logout')" method="post" as="button" class="block text-sm text-white/80 hover:text-white py-1">
                    {{ t('nav.logout') }}
                </Link>
            </div>
        </nav>

        <!-- Flash messages -->
        <div v-if="page.props.flash?.success"
             class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-accent/10 border border-accent/30 text-accent-dark rounded-lg px-4 py-3 text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                {{ page.props.flash.success }}
            </div>
        </div>

        <!-- Page header -->
        <header v-if="$slots.header" class="bg-white border-b border-slate-200">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-5">
                <slot name="header" />
            </div>
        </header>

        <!-- Main content -->
        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
