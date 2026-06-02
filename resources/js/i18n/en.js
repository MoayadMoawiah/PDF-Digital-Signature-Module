export default {
    app: {
        name: 'PDF Signature',
        tagline: 'Secure, self-hosted digital signing',
    },

    nav: {
        documents: 'Documents',
        dashboard: 'Dashboard',
        profile:   'Profile',
        logout:    'Log Out',
        login:     'Log In',
    },

    status: {
        draft:            'Draft',
        pending:          'Pending',
        partially_signed: 'Partially Signed',
        completed:        'Completed',
        expired:          'Expired',
        viewed:           'Viewed',
        signed:           'Signed',
        rejected:         'Rejected',
    },

    documents: {
        title:           'Documents',
        new_document:    'New Document',
        upload_pdf:      'Upload PDF',
        document_title:  'Document Title',
        expires_at:      'Expiry Date',
        optional:        '(optional)',
        no_documents:    'No documents yet',
        created_at:      'Created',
        signers:         'Signers',
        actions:         'Actions',
        view:            'View',
        download:        'Download',
        delete:          'Delete',
        download_signed: 'Download Signed PDF',
        audit_log:       'Audit Log',
        drop_pdf:        'Drop PDF here or click to select',
        max_size:        'Max file size: {size} MB',
        pdf_only:        'PDF files only',
    },

    signers: {
        title:            'Signers',
        add_signer:       'Add Signer',
        name:             'Name',
        email:            'Email',
        expires_at:       'Expiry Date',
        remove:           'Remove',
        signing_order:    'Signing Order',
        signed_at:        'Signed At',
        invitation_sent:  'Invitation Sent',
        rejection_reason: 'Rejection Reason',
    },

    signing: {
        title:          'Sign Document',
        document:       'Document',
        signer:         'Signer',
        draw_signature: 'Draw your signature',
        clear:          'Clear',
        sign:           'Sign Document',
        reject:         'Reject',
        reject_title:   'Reject Signing',
        reject_reason:  'Reason for rejection',
        reject_confirm: 'Confirm Rejection',
        cancel:         'Cancel',
        draw_first:     'Please draw your signature first',
        expires_in:     'Link expires in',
        expired_on:     'Link expired on',
        page:           'Page',
        of:             'of',
        prev:           'Previous',
        next:           'Next',
    },

    complete: {
        title:   'Successfully Signed',
        message: 'Thank you! The document has been signed successfully.',
        sub:     'The document sender has been notified.',
    },

    rejected: {
        title:   'Signing Rejected',
        message: 'The document signing has been rejected.',
        sub:     'The document sender has been notified with your reason.',
    },

    expired: {
        title:   'Link Expired',
        message: 'This signing link has expired.',
        sub:     'Please contact the document sender to request a new link.',
    },

    audit: {
        title:  'Audit Log',
        action: 'Action',
        signer: 'Signer',
        ip:     'IP Address',
        date:   'Date & Time',
        actions: {
            document_created:    'Document Created',
            invitation_sent:     'Invitation Sent',
            document_viewed:     'Document Viewed',
            signed:              'Signed',
            rejected:            'Rejected',
            expired:             'Expired',
            signed_pdf_generated: 'Signed PDF Generated',
        },
    },

    errors: {
        required:       'This field is required',
        invalid_email:  'Invalid email address',
        file_too_large: 'File exceeds the maximum allowed size',
        pdf_only:       'Only PDF files are allowed',
        min_signers:    'At least one signer is required',
    },

    confirm: {
        delete_document: 'Are you sure you want to delete this document?',
    },
};
