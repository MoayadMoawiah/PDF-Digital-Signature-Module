<?php

return [
    'default_expiry_days' => (int) env('SIGNATURE_DEFAULT_EXPIRY_DAYS', 7),
    'max_file_size_mb'    => (int) env('SIGNATURE_MAX_FILE_SIZE_MB', 20),
    'company_name'        => env('SIGNATURE_COMPANY_NAME', 'Your Organization'),
    'company_logo_url'    => env('SIGNATURE_COMPANY_LOGO_URL', ''),
    'storage_disk'        => 'local',
    'documents_path'      => 'documents',
];
