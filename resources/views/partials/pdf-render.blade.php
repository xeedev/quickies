{{-- Shared HTML → PDF renderer. Rasterizes HTML inside a clean, isolated iframe
     (no Tailwind / oklch styles) so html2canvas never chokes on modern colours,
     then paginates the result across A4 pages. --}}
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
<script>
    window.htmlToPdfDoc = async function (innerHtml, filename, options = {}) {
        const { jsPDF } = window.jspdf;
        const iframe = document.createElement('iframe');
        Object.assign(iframe.style, { position: 'fixed', left: '-10000px', top: '0', width: '794px', height: '1123px', border: '0' });
        document.body.appendChild(iframe);
        const doc = iframe.contentDocument;
        const fontFamily = options.fontFamily || "Georgia, 'Times New Roman', serif";
        doc.open();
        doc.write(`<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            * { box-sizing: border-box; color: #111827; border-color: #cbd5e1; }
            body { margin: 0; padding: 48px; width: 794px; background: #ffffff; color: #111827; font-family: ${fontFamily}; font-size: 15px; line-height: 1.6; word-wrap: break-word; }
            h1 { font-size: 26px; margin: .6em 0 .3em; } h2 { font-size: 21px; margin: .6em 0 .3em; } h3 { font-size: 17px; margin: .5em 0 .3em; }
            p { margin: 0 0 10px; } a { color: #1d4ed8; text-decoration: underline; }
            ul, ol { padding-left: 24px; margin: 8px 0; } li { margin: 2px 0; }
            code { background: #f1f5f9; padding: 1px 5px; border-radius: 4px; font-family: ui-monospace, monospace; font-size: .9em; }
            pre { background: #f1f5f9; padding: 12px; border-radius: 8px; overflow: auto; } pre code { background: none; padding: 0; }
            blockquote { border-left: 3px solid #94a3b8; padding-left: 12px; color: #475569; margin: 8px 0; }
            table { border-collapse: collapse; width: 100%; margin: 8px 0; } th, td { border: 1px solid #cbd5e1; padding: 6px 9px; text-align: left; }
            img { max-width: 100%; } hr { border: none; border-top: 1px solid #cbd5e1; margin: 16px 0; }
        </style></head><body>${innerHtml}</body></html>`);
        doc.close();

        // Give images / layout a moment to settle.
        await new Promise((r) => setTimeout(r, 350));

        try {
            const canvas = await html2canvas(doc.body, { scale: 2, backgroundColor: '#ffffff', windowWidth: 794, useCORS: true, imageTimeout: 5000 });
            const pdf = new jsPDF({ orientation: 'p', unit: 'pt', format: 'a4' });
            const pw = pdf.internal.pageSize.getWidth();
            const ph = pdf.internal.pageSize.getHeight();
            const imgH = (canvas.height * pw) / canvas.width;
            const imgData = canvas.toDataURL('image/jpeg', 0.95);
            let heightLeft = imgH;
            let pos = 0;
            pdf.addImage(imgData, 'JPEG', 0, pos, pw, imgH);
            heightLeft -= ph;
            while (heightLeft > 0) {
                pos = heightLeft - imgH;
                pdf.addPage();
                pdf.addImage(imgData, 'JPEG', 0, pos, pw, imgH);
                heightLeft -= ph;
            }
            pdf.save(filename);
        } finally {
            iframe.remove();
        }
    };
</script>
