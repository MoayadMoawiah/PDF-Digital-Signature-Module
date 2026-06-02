<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    signers: { type: Array, required: true },
    errors:  { type: Object, default: () => ({}) },
});

const emit = defineEmits(['update:signers']);

function addSigner() {
    emit('update:signers', [
        ...props.signers,
        { name: '', email: '', expires_at: '' },
    ]);
}

function removeSigner(index) {
    const updated = props.signers.filter((_, i) => i !== index);
    emit('update:signers', updated);
}

function updateSigner(index, field, value) {
    const updated = props.signers.map((s, i) =>
        i === index ? { ...s, [field]: value } : s
    );
    emit('update:signers', updated);
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700 uppercase tracking-wide">
                {{ t('signers.title') }}
            </h3>
            <button type="button"
                    @click="addSigner"
                    class="inline-flex items-center gap-1.5 text-sm text-primary hover:text-primary-dark font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ t('signers.add_signer') }}
            </button>
        </div>

        <div v-for="(signer, index) in signers" :key="index"
             class="bg-slate-50 border border-slate-200 rounded-lg p-4 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    {{ t('signers.signing_order') }} {{ index + 1 }}
                </span>
                <button v-if="signers.length > 1"
                        type="button"
                        @click="removeSigner(index)"
                        class="text-danger/70 hover:text-danger text-sm transition-colors">
                    {{ t('signers.remove') }}
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        {{ t('signers.name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           :value="signer.name"
                           @input="updateSigner(index, 'name', $event.target.value)"
                           class="w-full rounded-lg border-slate-300 text-sm focus:ring-primary focus:border-primary"
                           :placeholder="t('signers.name')" />
                    <p v-if="errors[`signers.${index}.name`]" class="mt-1 text-xs text-danger">
                        {{ errors[`signers.${index}.name`] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        {{ t('signers.email') }} <span class="text-danger">*</span>
                    </label>
                    <input type="email"
                           :value="signer.email"
                           @input="updateSigner(index, 'email', $event.target.value)"
                           class="w-full rounded-lg border-slate-300 text-sm focus:ring-primary focus:border-primary"
                           :placeholder="t('signers.email')" />
                    <p v-if="errors[`signers.${index}.email`]" class="mt-1 text-xs text-danger">
                        {{ errors[`signers.${index}.email`] }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-1">
                        {{ t('signers.expires_at') }}
                        <span class="text-slate-400 font-normal">{{ t('documents.optional') }}</span>
                    </label>
                    <input type="datetime-local"
                           :value="signer.expires_at"
                           @input="updateSigner(index, 'expires_at', $event.target.value)"
                           class="w-full rounded-lg border-slate-300 text-sm focus:ring-primary focus:border-primary" />
                </div>
            </div>
        </div>

        <p v-if="signers.length === 0" class="text-sm text-slate-400 text-center py-4">
            {{ t('errors.min_signers') }}
        </p>

        <p v-if="errors.signers" class="text-sm text-danger">{{ errors.signers }}</p>
    </div>
</template>
