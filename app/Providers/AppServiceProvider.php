<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::share('quickies', $this->quickies());
    }

    /**
     * The registry of available tools, shared with every view so the
     * dashboard and navigation stay in sync from a single source of truth.
     *
     * @return array<int, array<string, string>>
     */
    private function quickies(): array
    {
        return [
            [
                'name' => 'Color Palette',
                'href' => '/color-palette',
                'description' => 'Preview color codes and compare how they pair together.',
                'category' => 'Design',
                'from' => 'from-fuchsia-500',
                'to' => 'to-pink-500',
                'accent' => 'fuchsia',
                'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01',
            ],
            [
                'name' => 'PNG to SVG',
                'href' => '/png-to-svg',
                'description' => 'Trace raster images into scalable vector graphics.',
                'category' => 'Image',
                'from' => 'from-green-500',
                'to' => 'to-emerald-500',
                'accent' => 'emerald',
                'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            [
                'name' => 'Image Compressor',
                'href' => '/image-compressor',
                'description' => 'Shrink JPG, PNG and WebP images without losing quality.',
                'category' => 'Image',
                'from' => 'from-blue-500',
                'to' => 'to-cyan-500',
                'accent' => 'cyan',
                'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12',
            ],
            [
                'name' => 'Image Cropper',
                'href' => '/image-cropper',
                'description' => 'Crop, rotate and flip images and export to any format.',
                'category' => 'Image',
                'from' => 'from-orange-500',
                'to' => 'to-amber-500',
                'accent' => 'orange',
                'icon' => 'M3 4h3M3 4v3m18-3h-3m3 0v3M3 20h3m-3 0v-3m18 3h-3m3 0v-3M8 8h8v8H8z',
            ],
            [
                'name' => 'QR Code Generator',
                'href' => '/qr-code',
                'description' => 'Turn links and text into scannable QR codes instantly.',
                'category' => 'Generators',
                'from' => 'from-indigo-500',
                'to' => 'to-violet-500',
                'accent' => 'indigo',
                'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 4v-4m0 4h2m4 0v-4m0 0h-4m4 0h.01M14 14h.01',
            ],
            [
                'name' => 'Password Generator',
                'href' => '/password-generator',
                'description' => 'Create strong, random passwords with custom rules.',
                'category' => 'Security',
                'from' => 'from-rose-500',
                'to' => 'to-red-500',
                'accent' => 'rose',
                'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z',
            ],
            [
                'name' => 'Hash Generator',
                'href' => '/hash-generator',
                'description' => 'Generate SHA-1, SHA-256, SHA-384 and SHA-512 hashes.',
                'category' => 'Security',
                'from' => 'from-fuchsia-500',
                'to' => 'to-purple-500',
                'accent' => 'purple',
                'icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14',
            ],
            [
                'name' => 'Base64 Encoder',
                'href' => '/base64',
                'description' => 'Encode and decode text or files to and from Base64.',
                'category' => 'Developer',
                'from' => 'from-teal-500',
                'to' => 'to-cyan-500',
                'accent' => 'teal',
                'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            ],
            [
                'name' => 'JSON Formatter',
                'href' => '/json-formatter',
                'description' => 'Beautify, minify and validate JSON in the browser.',
                'category' => 'Developer',
                'from' => 'from-amber-500',
                'to' => 'to-yellow-500',
                'accent' => 'amber',
                'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            [
                'name' => 'Word Counter',
                'href' => '/word-counter',
                'description' => 'Count words, characters, sentences and reading time.',
                'category' => 'Text',
                'from' => 'from-sky-500',
                'to' => 'to-blue-500',
                'accent' => 'sky',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
            [
                'name' => 'UUID Generator',
                'href' => '/uuid-generator',
                'description' => 'Generate RFC-4122 version 4 UUIDs in bulk.',
                'category' => 'Developer',
                'from' => 'from-lime-500',
                'to' => 'to-green-500',
                'accent' => 'lime',
                'icon' => 'M4 6h16M4 10h16M4 14h10M4 18h10M17 15l2 2 4-4',
            ],
            [
                'name' => 'Lorem Ipsum',
                'href' => '/lorem-ipsum',
                'description' => 'Generate placeholder paragraphs, sentences and words.',
                'category' => 'Text',
                'from' => 'from-slate-500',
                'to' => 'to-slate-400',
                'accent' => 'slate',
                'icon' => 'M4 6h16M4 12h16M4 18h7',
            ],
            [
                'name' => 'Color Picker',
                'href' => '/color-picker',
                'description' => 'Pick colors, sample from screen and export hex, RGB and HSL.',
                'category' => 'Design',
                'from' => 'from-pink-500',
                'to' => 'to-rose-500',
                'accent' => 'pink',
                'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z',
            ],
            [
                'name' => 'Images to PDF',
                'href' => '/images-to-pdf',
                'description' => 'Combine multiple images into a single downloadable PDF.',
                'category' => 'Document',
                'from' => 'from-red-500',
                'to' => 'to-orange-500',
                'accent' => 'red',
                'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            ],
            [
                'name' => 'PDF Compressor',
                'href' => '/pdf-compressor',
                'description' => 'Shrink PDF file size by re-rendering pages at lower quality.',
                'category' => 'Document',
                'from' => 'from-rose-500',
                'to' => 'to-pink-500',
                'accent' => 'rose',
                'icon' => 'M9 12h6m-3-3v6m-7 5h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            ],
            [
                'name' => 'Word to PDF',
                'href' => '/word-to-pdf',
                'description' => 'Convert Word .docx documents into a formatted PDF file.',
                'category' => 'Document',
                'from' => 'from-blue-600',
                'to' => 'to-sky-500',
                'accent' => 'blue',
                'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
            [
                'name' => 'PDF to Word',
                'href' => '/pdf-to-word',
                'description' => 'Extract text from a PDF into an editable Word document.',
                'category' => 'Document',
                'from' => 'from-sky-500',
                'to' => 'to-blue-600',
                'accent' => 'sky',
                'icon' => 'M12 9v6m3-3H9m-4 9h14a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
            ],
            [
                'name' => 'Diff Checker',
                'href' => '/diff-checker',
                'description' => 'Compare two texts and highlight added and removed lines.',
                'category' => 'Developer',
                'from' => 'from-emerald-500',
                'to' => 'to-teal-500',
                'accent' => 'emerald',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            ],
            [
                'name' => 'Regex Tester',
                'href' => '/regex-tester',
                'description' => 'Test regular expressions and inspect live match highlights.',
                'category' => 'Developer',
                'from' => 'from-violet-500',
                'to' => 'to-purple-500',
                'accent' => 'violet',
                'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4',
            ],
            [
                'name' => 'Timestamp Converter',
                'href' => '/timestamp',
                'description' => 'Convert between Unix timestamps and human-readable dates.',
                'category' => 'Developer',
                'from' => 'from-amber-500',
                'to' => 'to-orange-500',
                'accent' => 'amber',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            [
                'name' => 'JWT Decoder',
                'href' => '/jwt-decoder',
                'description' => 'Decode JSON Web Token header and payload in your browser.',
                'category' => 'Security',
                'from' => 'from-indigo-500',
                'to' => 'to-blue-500',
                'accent' => 'indigo',
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            ],
            [
                'name' => 'Markdown Previewer',
                'href' => '/markdown',
                'description' => 'Write Markdown and preview the rendered HTML instantly.',
                'category' => 'Text',
                'from' => 'from-cyan-500',
                'to' => 'to-sky-500',
                'accent' => 'cyan',
                'icon' => 'M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm2 10V9l3 3 3-3v6m3 0l2-2m0 0l-2-2m2 2h-4',
            ],
        ];
    }
}
