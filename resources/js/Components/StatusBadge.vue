<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    status: { type: String, required: true },
    size:   { type: String, default: 'sm' }, // 'xs' | 'sm' | 'md'
});

const colorMap = {
    draft:            'bg-slate-100 text-slate-600',
    pending:          'bg-yellow-100 text-yellow-800',
    partially_signed: 'bg-blue-100 text-blue-800',
    completed:        'bg-green-100 text-green-800',
    expired:          'bg-red-100 text-red-700',
    viewed:           'bg-blue-50 text-blue-600',
    signed:           'bg-green-100 text-green-700',
    rejected:         'bg-red-100 text-red-700',
};

const sizeMap = {
    xs: 'text-xs px-1.5 py-0.5',
    sm: 'text-xs px-2 py-1',
    md: 'text-sm px-3 py-1',
};

const classes = computed(() => [
    'inline-flex items-center rounded-full font-medium whitespace-nowrap',
    colorMap[props.status] ?? 'bg-gray-100 text-gray-600',
    sizeMap[props.size] ?? sizeMap.sm,
]);

const label = computed(() => t(`status.${props.status}`, props.status));
</script>

<template>
    <span :class="classes">{{ label }}</span>
</template>
