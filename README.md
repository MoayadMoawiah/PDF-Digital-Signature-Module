# PDF Signature — Self-Hosted Digital Signing

A production-ready, self-hosted PDF digital signature module built with **Laravel 11**, **Vue 3 + Inertia.js**, and **pdf-lib**. All document processing happens on your server — no third-party signature services, no subscriptions, full data sovereignty.

---

## Features

- Upload PDFs and invite signers via email
- Public signing page with drawn signature pad (mouse + touch)
- PDF.js document viewer for signers
- Signature embedded directly into PDF via `pdf-lib` (Node.js)
- Multi-signer support with ordered signing chain
- Rejection flow with reason capture + owner notification
- Full audit log timeline per document
- Bilingual interface (Arabic RTL + English LTR) — persists per user
- Secure token-based signing links (64-char hex, single-use)
- Rate limiting, CSRF protection, private storage (no public file exposure)
- Queue-based email delivery (database driver, upgradeable to Redis)

---

## Tech Stack

| Layer         | Technology                                      |
|---------------|-------------------------------------------------|
| Backend       | Laravel 11 (PHP 8.2+)                           |
| Frontend      | Vue 3 + Inertia.js + Vite                       |
| Styling       | Tailwind CSS v3 + `tailwindcss-rtl`             |
| PDF Embedding | `pdf-lib` (Node.js CLI script)                  |
| PDF Viewer    | `pdfjs-dist`                                    |
| Signature Pad | `signature_pad`                                 |
| Database      | MySQL 8                                         |
| Queue         | Laravel Queue (database driver)                 |
| Email         | Laravel Mail (SMTP — configurable via `.env`)   |
| File Storage  | Local disk under `storage/app/` (private)       |
| Auth          | Laravel Breeze                                  |
| i18n          | `vue-i18n` (AR/EN)                              |

---

## Prerequisites

- **PHP** >= 8.2 with extensions: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`
- **Composer** >= 2.x
- **Node.js** >= 18 (20 recommended)
- **npm** >= 9
- **MySQL** 8.x
- A configured **SMTP** server for email

---

## Installation

### 1. Clone & Install Dependencies

```bash
git clone <your-repo-url> pdf-signature
cd pdf-signature
composer install
npm install
```

### 2. Install Node.js PDF Script Dependencies

```bash
cd scripts
npm install
cd ..
```

### 3. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure:

```env
APP_URL=https://yourdomain.com
APP_LOCALE=ar   # or 'en'

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pdf_signature
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_smtp_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="PDF Signature"

SIGNATURE_DEFAULT_EXPIRY_DAYS=7
SIGNATURE_MAX_FILE_SIZE_MB=20
SIGNATURE_COMPANY_NAME="Your Organization"
SIGNATURE_COMPANY_LOGO_URL=https://yourdomain.com/logo.png
```

### 4. Database Setup

```bash
php artisan migrate
```

### 5. Storage Link

```bash
php artisan storage:link
```

### 6. Create Admin User

```bash
php artisan tinker
>>> \App\Models\User::factory()->create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password')]);
```

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Start Queue Worker

The queue worker processes email sending and must run continuously:

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3
```

For production, use a process manager like **Supervisor**:

```ini
[program:pdf-signature-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/pdf-signature/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/pdf-signature/storage/logs/queue.log
stopwaitsecs=3600
```

### 9. Start Application

For local development:

```bash
php artisan serve
```

Application available at: http://localhost:8000

---

## Production Deployment

### Nginx Configuration

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/pdf-signature/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Post-deploy Commands

```bash
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link
```

---

## Application Structure

```
pdf-signature/
├── app/
│   ├── Enums/
│   │   ├── DocumentStatus.php       # draft|pending|partially_signed|completed|expired
│   │   └── SignerStatus.php         # pending|viewed|signed|rejected|expired
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   ├── DocumentController.php   # CRUD + download
│   │   │   │   └── AuditLogController.php   # Timeline view
│   │   │   └── Signing/
│   │   │       └── SigningController.php    # Public sign/reject/pdf
│   │   └── Middleware/
│   │       └── ValidateSigningToken.php    # Token validation + expiry
│   ├── Jobs/
│   │   └── SendSigningInvitation.php       # Queued email dispatch
│   ├── Mail/
│   │   ├── SigningInvitationMail.php
│   │   └── RejectionNotificationMail.php
│   ├── Models/
│   │   ├── Document.php
│   │   ├── Signer.php
│   │   └── AuditLog.php
│   └── Services/
│       ├── SigningTokenService.php          # Token generation + assignment
│       └── PdfSignatureEmbedService.php     # PDF chaining + Node.js invocation
├── scripts/
│   ├── embed-signature.js                  # pdf-lib Node.js CLI
│   └── package.json
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   │   ├── SignatureCanvas.vue   # signature_pad wrapper
│   │   │   ├── PdfViewer.vue         # pdfjs-dist viewer
│   │   │   ├── StatusBadge.vue       # Status → color label
│   │   │   ├── LanguageSwitcher.vue  # AR/EN toggle
│   │   │   └── SignerForm.vue        # Dynamic signer rows
│   │   ├── i18n/
│   │   │   ├── ar.js
│   │   │   └── en.js
│   │   └── Pages/
│   │       ├── Admin/Documents/     # Index, Create, Show
│   │       └── Signing/             # Sign, Complete, Rejected, Expired
│   └── lang/ar|en/messages.php
└── database/migrations/             # 3 migrations
```

---

## Security Notes

- Signing tokens are 64-char cryptographically random hex strings (`bin2hex(random_bytes(32))`)
- PDFs stored under `storage/app/` (private, not web-accessible)
- All file downloads go through authenticated controller methods
- `signers.signature_data` is purged from the database after PDF embedding
- SHA-256 hash of original PDF is recorded in audit log on upload
- Path traversal protection in `PdfSignatureEmbedService`
- Rate limiting: 10 requests/minute per IP on public signing routes
- CSRF protection on all POST routes

---

## Queue Upgrade to Redis

To switch from database queue to Redis for production:

1. Update `.env`:
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

2. Restart queue worker.

---

## Troubleshooting

**PDF embedding fails:**
- Ensure `node` is in the system PATH accessible by the PHP process
- Run `node scripts/embed-signature.js --help` to verify the script executes
- Check `storage/logs/laravel.log` for detailed error messages

**Emails not sending:**
- Confirm SMTP credentials in `.env`
- Check queue worker is running: `php artisan queue:work`
- Test with: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('you@test.com')->subject('test'));`

**Migrations fail:**
- Ensure MySQL user has CREATE TABLE privileges
- Verify `DB_DATABASE` exists: `CREATE DATABASE pdf_signature CHARACTER SET utf8mb4;`
#   P D F - D i g i t a l - S i g n a t u r e - M o d u l e  
 