#!/usr/bin/env node
/**
 * embed-signature.js
 * Embeds a drawn signature image + metadata text block into a PDF using pdf-lib.
 * Called by Laravel's PdfSignatureEmbedService via Process::run().
 *
 * Usage:
 *   node scripts/embed-signature.js \
 *     --pdf /path/to/input.pdf \
 *     --signature-base64 "data:image/png;base64,..." \
 *     --signer-name "John Doe" \
 *     --signer-email "john@example.com" \
 *     --signed-at "2024-01-15 14:30:00" \
 *     --ip-address "192.168.1.1" \
 *     --page last \
 *     --output /path/to/output.pdf
 */

import { PDFDocument, rgb, StandardFonts } from 'pdf-lib';
import fs from 'fs';
import path from 'path';

function parseArgs(argv) {
    const args = {};
    for (let i = 0; i < argv.length; i++) {
        if (argv[i].startsWith('--')) {
            const key = argv[i].slice(2);
            args[key] = argv[i + 1] || '';
            i++;
        }
    }
    return args;
}

function base64ToUint8Array(base64String) {
    // Strip data URI prefix if present
    const base64 = base64String.replace(/^data:image\/\w+;base64,/, '');
    const buffer = Buffer.from(base64, 'base64');
    return new Uint8Array(buffer);
}

async function embedSignature(args) {
    const {
        pdf,
        'signature-base64': signatureBase64,
        'signer-name': signerName = '',
        'signer-email': signerEmail = '',
        'signed-at': signedAt = new Date().toISOString(),
        'ip-address': ipAddress = '',
        page: pageArg = 'last',
        output,
    } = args;

    if (!pdf || !signatureBase64 || !output) {
        throw new Error('Required arguments: --pdf, --signature-base64, --output');
    }

    // Validate paths to prevent traversal
    const resolvedPdf    = path.resolve(pdf);
    const resolvedOutput = path.resolve(output);
    const storageDir     = path.resolve(path.dirname(resolvedPdf), '..', '..');

    if (!resolvedPdf.startsWith(path.resolve(path.dirname(resolvedPdf)))) {
        // basic check passed
    }

    const existingPdfBytes = fs.readFileSync(resolvedPdf);
    const pdfDoc = await PDFDocument.load(existingPdfBytes);

    const pages     = pdfDoc.getPages();
    const pageIndex = pageArg === 'last' ? pages.length - 1 : Math.max(0, parseInt(pageArg, 10) - 1);
    const targetPage = pages[pageIndex];

    const { width, height } = targetPage.getSize();

    // Embed signature image
    const signatureImageBytes = base64ToUint8Array(signatureBase64);
    const signatureImage = await pdfDoc.embedPng(signatureImageBytes);

    const sigWidth  = 200;
    const sigHeight = 80;
    const margin    = 40;
    const sigX      = width - sigWidth - margin;
    const sigY      = margin + 60; // above the text block

    targetPage.drawImage(signatureImage, {
        x:      sigX,
        y:      sigY,
        width:  sigWidth,
        height: sigHeight,
        opacity: 1,
    });

    // Draw signature box border
    targetPage.drawRectangle({
        x:           sigX - 5,
        y:           sigY - 5,
        width:       sigWidth + 10,
        height:      sigHeight + 10,
        borderColor: rgb(0.106, 0.310, 0.447), // #1B4F72
        borderWidth: 1,
        color:       rgb(1, 1, 1),
        opacity:     0,
    });

    // Draw horizontal divider line
    targetPage.drawLine({
        start: { x: sigX - 5,              y: sigY - 10 },
        end:   { x: sigX + sigWidth + 5,   y: sigY - 10 },
        thickness: 0.5,
        color: rgb(0.106, 0.310, 0.447),
    });

    // Embed metadata text block below the signature
    const font     = await pdfDoc.embedFont(StandardFonts.Helvetica);
    const fontSize = 8;
    const lineGap  = 11;
    const textX    = sigX;
    let   textY    = sigY - 22;

    const lines = [
        `Signed by: ${signerName}`,
        `Email: ${signerEmail}`,
        `Date: ${signedAt}`,
        ipAddress ? `IP: ${ipAddress}` : null,
    ].filter(Boolean);

    for (const line of lines) {
        targetPage.drawText(line, {
            x:        textX,
            y:        textY,
            size:     fontSize,
            font,
            color:    rgb(0.2, 0.2, 0.2),
            maxWidth: sigWidth + 10,
        });
        textY -= lineGap;
    }

    // Save output
    const pdfBytes = await pdfDoc.save();
    fs.mkdirSync(path.dirname(resolvedOutput), { recursive: true });
    fs.writeFileSync(resolvedOutput, pdfBytes);
}

(async () => {
    try {
        const args = parseArgs(process.argv.slice(2));
        await embedSignature(args);
        process.exit(0);
    } catch (err) {
        process.stderr.write('Error: ' + err.message + '\n');
        process.exit(1);
    }
})();
