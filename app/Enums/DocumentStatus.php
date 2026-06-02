<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Draft           = 'draft';
    case Pending         = 'pending';
    case PartiallySigned = 'partially_signed';
    case Completed       = 'completed';
    case Expired         = 'expired';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Draft           => $locale === 'ar' ? 'مسودة'          : 'Draft',
            self::Pending         => $locale === 'ar' ? 'قيد الانتظار'   : 'Pending',
            self::PartiallySigned => $locale === 'ar' ? 'موقع جزئياً'    : 'Partially Signed',
            self::Completed       => $locale === 'ar' ? 'مكتمل'          : 'Completed',
            self::Expired         => $locale === 'ar' ? 'منتهي الصلاحية' : 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft           => 'gray',
            self::Pending         => 'yellow',
            self::PartiallySigned => 'blue',
            self::Completed       => 'green',
            self::Expired         => 'red',
        };
    }
}
