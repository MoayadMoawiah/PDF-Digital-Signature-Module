export default {
    app: {
        name: 'توقيع PDF',
        tagline: 'توقيع رقمي آمن وذاتي الاستضافة',
    },

    nav: {
        documents:  'الوثائق',
        dashboard:  'لوحة التحكم',
        profile:    'الملف الشخصي',
        logout:     'تسجيل الخروج',
        login:      'تسجيل الدخول',
    },

    status: {
        draft:            'مسودة',
        pending:          'قيد الانتظار',
        partially_signed: 'موقع جزئياً',
        completed:        'مكتمل',
        expired:          'منتهي الصلاحية',
        viewed:           'تمت المشاهدة',
        signed:           'موقع',
        rejected:         'مرفوض',
    },

    documents: {
        title:           'الوثائق',
        new_document:    'وثيقة جديدة',
        upload_pdf:      'تحميل ملف PDF',
        document_title:  'عنوان الوثيقة',
        expires_at:      'تاريخ انتهاء الصلاحية',
        optional:        '(اختياري)',
        no_documents:    'لا توجد وثائق بعد',
        created_at:      'تاريخ الإنشاء',
        signers:         'الموقّعون',
        actions:         'الإجراءات',
        view:            'عرض',
        download:        'تحميل',
        delete:          'حذف',
        download_signed: 'تحميل PDF الموقّع',
        audit_log:       'سجل التدقيق',
        drop_pdf:        'اسحب ملف PDF هنا أو انقر للاختيار',
        max_size:        'الحد الأقصى لحجم الملف: {size} ميجابايت',
        pdf_only:        'ملفات PDF فقط',
    },

    signers: {
        title:        'الموقّعون',
        add_signer:   'إضافة موقّع',
        name:         'الاسم',
        email:        'البريد الإلكتروني',
        expires_at:   'تاريخ انتهاء الصلاحية',
        remove:       'إزالة',
        signing_order: 'ترتيب التوقيع',
        signed_at:    'وقت التوقيع',
        invitation_sent: 'تم إرسال الدعوة',
        rejection_reason: 'سبب الرفض',
    },

    signing: {
        title:          'توقيع الوثيقة',
        document:       'الوثيقة',
        signer:         'الموقّع',
        draw_signature: 'ارسم توقيعك',
        clear:          'مسح',
        sign:           'توقيع الوثيقة',
        reject:         'رفض التوقيع',
        reject_title:   'رفض التوقيع',
        reject_reason:  'سبب الرفض',
        reject_confirm: 'تأكيد الرفض',
        cancel:         'إلغاء',
        draw_first:     'يرجى رسم توقيعك أولاً',
        expires_in:     'ينتهي الرابط خلال',
        expired_on:     'انتهت صلاحية الرابط في',
        page:           'الصفحة',
        of:             'من',
        prev:           'السابق',
        next:           'التالي',
    },

    complete: {
        title:    'تم التوقيع بنجاح',
        message:  'شكراً لك! تم توقيع الوثيقة بنجاح.',
        sub:      'تم إعلام مُرسل الوثيقة.',
    },

    rejected: {
        title:   'تم رفض التوقيع',
        message: 'تم رفض التوقيع على الوثيقة بنجاح.',
        sub:     'تم إعلام مُرسل الوثيقة بسبب الرفض.',
    },

    expired: {
        title:   'انتهت صلاحية الرابط',
        message: 'انتهت صلاحية رابط التوقيع.',
        sub:     'يرجى التواصل مع مُرسل الوثيقة للحصول على رابط جديد.',
    },

    audit: {
        title:          'سجل التدقيق',
        action:         'الإجراء',
        signer:         'الموقّع',
        ip:             'عنوان IP',
        date:           'التاريخ والوقت',
        actions: {
            document_created:    'تم إنشاء الوثيقة',
            invitation_sent:     'تم إرسال الدعوة',
            document_viewed:     'تمت مشاهدة الوثيقة',
            signed:              'تم التوقيع',
            rejected:            'تم الرفض',
            expired:             'انتهت الصلاحية',
            signed_pdf_generated: 'تم إنشاء PDF الموقّع',
        },
    },

    errors: {
        required:     'هذا الحقل مطلوب',
        invalid_email: 'البريد الإلكتروني غير صحيح',
        file_too_large: 'حجم الملف أكبر من الحد المسموح',
        pdf_only:     'يُسمح بملفات PDF فقط',
        min_signers:  'يجب إضافة موقّع واحد على الأقل',
    },

    confirm: {
        delete_document: 'هل أنت متأكد من حذف هذه الوثيقة؟',
    },
};
