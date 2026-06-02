<?php

namespace App\Enums;

enum SignerStatus: string
{
    case Pending  = 'pending';
    case Viewed   = 'viewed';
    case Signed   = 'signed';
    case Rejected = 'rejected';
    case Expired  = 'expired';

    public function label(string $locale = 'en'): string
    {
        return match ($this) {
            self::Pending  => $locale === 'ar' ? 'قيد الانتظار'   : 'Pending',
            self::Viewed   => $locale === 'ar' ? 'تمت المشاهدة'   : 'Viewed',
            self::Signed   => $locale === 'ar' ? 'موقع'           : 'Signed',
            self::Rejected => $locale === 'ar' ? 'مرفوض'          : 'Rejected',
            self::Expired  => $locale === 'ar' ? 'منتهي الصلاحية' : 'Expired',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'yellow',
            self::Viewed   => 'blue',
            self::Signed   => 'green',
            self::Rejected => 'red',
            self::Expired  => 'gray',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::Signed, self::Rejected, self::Expired]);
    }
}
