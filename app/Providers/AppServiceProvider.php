<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // NOTE: VAS/DCB tools live in a separate method so the whole module can be
        // detached by removing the array_merge below, the vas() method, the
        // App\Http\Controllers\Vas namespace, the resources/views/quickies/vas
        // folder and the marked route block in routes/web.php.
        $tools = array_merge($this->quickies(), $this->vasQuickies());

        View::share('quickies', $tools);
        View::share('quickieCategories', $this->categoryOrder($tools));
    }

    /**
     * Ordered list of unique categories as they should appear on the dashboard.
     *
     * @param  array<int, array<string, string>>  $tools
     * @return array<int, string>
     */
    private function categoryOrder(array $tools): array
    {
        $order = [
            'Featured', 'VAS & DCB', 'Text', 'Data & Convert', 'Developer', 'Web & Network',
            'Date & Time', 'Generators', 'Security', 'Design', 'Image', 'Document',
        ];

        $present = array_values(array_unique(array_column($tools, 'category')));

        // Keep known order first, then append any unexpected categories.
        return array_values(array_unique(array_merge(
            array_values(array_intersect($order, $present)),
            array_diff($present, $order),
        )));
    }

    /**
     * The registry of available tools, shared with every view.
     *
     * @return array<int, array<string, string>>
     */
    private function quickies(): array
    {
        return [
            // ---- Text ----
            ['name' => 'Word Counter', 'href' => '/word-counter', 'category' => 'Text', 'from' => 'from-sky-500', 'to' => 'to-blue-500', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'description' => 'Count words, characters, sentences and reading time.'],
            ['name' => 'Case Converter', 'href' => '/case-converter', 'category' => 'Text', 'from' => 'from-sky-500', 'to' => 'to-cyan-500', 'icon' => 'M4 7V4h16v3M9 20h6M12 4v16', 'description' => 'Convert text between camelCase, snake_case, Title Case and more.'],
            ['name' => 'Remove Duplicate Lines', 'href' => '/remove-duplicates', 'category' => 'Text', 'from' => 'from-teal-500', 'to' => 'to-emerald-500', 'icon' => 'M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01', 'description' => 'Strip duplicate lines and clean up whitespace.'],
            ['name' => 'Sort & Shuffle Lines', 'href' => '/sort-lines', 'category' => 'Text', 'from' => 'from-cyan-500', 'to' => 'to-sky-500', 'icon' => 'M3 4h13M3 8h9M3 12h5m4 6l4 4 4-4m-4 4V4', 'description' => 'Sort lines alphabetically or numerically, reverse or shuffle.'],
            ['name' => 'Slug Generator', 'href' => '/slug', 'category' => 'Text', 'from' => 'from-lime-500', 'to' => 'to-green-500', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'description' => 'Create clean URL-friendly slugs from any text.'],
            ['name' => 'Markdown Previewer', 'href' => '/markdown', 'category' => 'Text', 'from' => 'from-cyan-500', 'to' => 'to-sky-500', 'icon' => 'M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm2 10V9l3 3 3-3v6m3 0l2-2m0 0l-2-2m2 2h-4', 'description' => 'Write Markdown and preview the rendered HTML instantly.'],
            ['name' => 'Lorem Ipsum', 'href' => '/lorem-ipsum', 'category' => 'Text', 'from' => 'from-slate-500', 'to' => 'to-slate-400', 'icon' => 'M4 6h16M4 12h16M4 18h7', 'description' => 'Generate placeholder paragraphs, sentences and words.'],

            // ---- Data & Convert ----
            ['name' => 'JSON Formatter', 'href' => '/json-formatter', 'category' => 'Data & Convert', 'from' => 'from-amber-500', 'to' => 'to-yellow-500', 'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'description' => 'Beautify, minify and validate JSON in the browser.'],
            ['name' => 'JSON ↔ YAML', 'href' => '/json-yaml', 'category' => 'Data & Convert', 'from' => 'from-orange-500', 'to' => 'to-amber-500', 'icon' => 'M7 8l-4 4 4 4m10-8l4 4-4 4M14 4l-4 16', 'description' => 'Convert between JSON and YAML in either direction.'],
            ['name' => 'CSV ↔ JSON', 'href' => '/csv-json', 'category' => 'Data & Convert', 'from' => 'from-green-500', 'to' => 'to-teal-500', 'icon' => 'M3 10h18M3 14h18M12 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z', 'description' => 'Convert CSV to JSON and JSON arrays back to CSV.'],
            ['name' => 'Base64 Encoder', 'href' => '/base64', 'category' => 'Data & Convert', 'from' => 'from-teal-500', 'to' => 'to-cyan-500', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'description' => 'Encode and decode text or files to and from Base64.'],
            ['name' => 'Base-N Encoder', 'href' => '/base-n', 'category' => 'Data & Convert', 'from' => 'from-cyan-500', 'to' => 'to-blue-500', 'icon' => 'M4 7V4h16v3M9 20h6M12 4v16', 'description' => 'Encode text across Base16, Base32, Base58 and Base64.'],

            // ---- Developer ----
            ['name' => 'Diff Checker', 'href' => '/diff-checker', 'category' => 'Developer', 'from' => 'from-emerald-500', 'to' => 'to-teal-500', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'description' => 'Compare two texts and highlight added and removed lines.'],
            ['name' => 'JSON Diff Viewer', 'href' => '/json-diff', 'category' => 'Developer', 'from' => 'from-emerald-500', 'to' => 'to-green-500', 'icon' => 'M7 8h10M7 12h4m-4 4h10M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z', 'description' => 'Compare two JSON objects and see what changed.'],
            ['name' => 'Regex Tester', 'href' => '/regex-tester', 'category' => 'Developer', 'from' => 'from-violet-500', 'to' => 'to-purple-500', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'description' => 'Test regular expressions and inspect live match highlights.'],
            ['name' => 'SQL Formatter', 'href' => '/sql-formatter', 'category' => 'Developer', 'from' => 'from-indigo-500', 'to' => 'to-violet-500', 'icon' => 'M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3-3.582 3-8 3-8-1.343-8-3zm0 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7', 'description' => 'Beautify and standardise SQL queries for many dialects.'],
            ['name' => 'Cron Builder', 'href' => '/cron', 'category' => 'Developer', 'from' => 'from-purple-500', 'to' => 'to-fuchsia-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'description' => 'Build and explain cron expressions in plain English.'],
            ['name' => 'JSON Schema Generator', 'href' => '/json-schema', 'category' => 'Developer', 'from' => 'from-amber-500', 'to' => 'to-orange-500', 'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z', 'description' => 'Infer a JSON Schema from an example JSON document.'],
            ['name' => 'Docker Compose Validator', 'href' => '/docker-compose', 'category' => 'Developer', 'from' => 'from-blue-500', 'to' => 'to-sky-500', 'icon' => 'M5 13h4v4H5v-4zm5 0h4v4h-4v-4zm5 0h4v4h-4v-4zM10 8h4v4h-4V8zm5 0h4v4h-4V8zM3 21c4 0 6-2 6-2', 'description' => 'Validate docker-compose YAML structure and services.'],
            ['name' => 'Token Counter', 'href' => '/token-counter', 'category' => 'Developer', 'from' => 'from-fuchsia-500', 'to' => 'to-pink-500', 'icon' => 'M7 8h10M7 12h6m-6 4h10M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z', 'description' => 'Estimate LLM token counts and cost for your prompts.'],

            // ---- Web & Network ----
            ['name' => 'URL Encoder', 'href' => '/url-encoder', 'category' => 'Web & Network', 'from' => 'from-blue-500', 'to' => 'to-indigo-500', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'description' => 'Encode and decode URLs and URI components.'],
            ['name' => 'Query String Parser', 'href' => '/query-parser', 'category' => 'Web & Network', 'from' => 'from-indigo-500', 'to' => 'to-blue-500', 'icon' => 'M8 9l3 3-3 3m5 0h3M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z', 'description' => 'Break a URL query string into readable key/value pairs.'],
            ['name' => 'cURL Converter', 'href' => '/curl-converter', 'category' => 'Web & Network', 'from' => 'from-sky-500', 'to' => 'to-cyan-500', 'icon' => 'M4 7l4-4m0 0l4 4M8 3v13m8 5l-4-4m0 0l-4 4m4-4V8', 'description' => 'Convert cURL commands into fetch and axios code.'],
            ['name' => 'HTTP Status Codes', 'href' => '/http-status', 'category' => 'Web & Network', 'from' => 'from-cyan-500', 'to' => 'to-teal-500', 'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'description' => 'Look up and learn every HTTP status code.'],
            ['name' => 'QR Code Generator', 'href' => '/qr-code', 'category' => 'Web & Network', 'from' => 'from-indigo-500', 'to' => 'to-violet-500', 'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 4v-4m0 4h2m4 0v-4m0 0h-4m4 0h.01M14 14h.01', 'description' => 'Turn links and text into scannable QR codes instantly.'],

            // ---- Date & Time ----
            ['name' => 'Timestamp Converter', 'href' => '/timestamp', 'category' => 'Date & Time', 'from' => 'from-amber-500', 'to' => 'to-orange-500', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'description' => 'Convert between Unix timestamps and human dates.'],
            ['name' => 'Timezone Converter', 'href' => '/timezone', 'category' => 'Date & Time', 'from' => 'from-orange-500', 'to' => 'to-red-500', 'icon' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0z M3 12h18M12 3a15 15 0 010 18 15 15 0 010-18', 'description' => 'Compare a moment in time across multiple timezones.'],
            ['name' => 'Business Days', 'href' => '/business-days', 'category' => 'Date & Time', 'from' => 'from-red-500', 'to' => 'to-rose-500', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'description' => 'Count working days between two dates, skipping weekends.'],

            // ---- Generators ----
            ['name' => 'ID Generator', 'href' => '/id-generator', 'category' => 'Generators', 'from' => 'from-lime-500', 'to' => 'to-green-500', 'icon' => 'M4 6h16M4 10h16M4 14h10M4 18h10M17 15l2 2 4-4', 'description' => 'Generate UUID, NanoID and ULID identifiers in bulk.'],
            ['name' => 'UUID Generator', 'href' => '/uuid-generator', 'category' => 'Generators', 'from' => 'from-green-500', 'to' => 'to-emerald-500', 'icon' => 'M4 6h16M4 10h16M4 14h10M4 18h7', 'description' => 'Generate RFC-4122 version 4 UUIDs quickly.'],
            ['name' => 'Mock JSON Generator', 'href' => '/mock-json', 'category' => 'Generators', 'from' => 'from-emerald-500', 'to' => 'to-teal-500', 'icon' => 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4', 'description' => 'Generate realistic mock JSON data from a schema.'],
            ['name' => 'Password Generator', 'href' => '/password-generator', 'category' => 'Generators', 'from' => 'from-rose-500', 'to' => 'to-red-500', 'icon' => 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z', 'description' => 'Create strong, random passwords with custom rules.'],

            // ---- Security ----
            ['name' => 'Hash Generator', 'href' => '/hash-generator', 'category' => 'Security', 'from' => 'from-fuchsia-500', 'to' => 'to-purple-500', 'icon' => 'M7 20l4-16m2 16l4-16M6 9h14M4 15h14', 'description' => 'Generate SHA-1, SHA-256, SHA-384 and SHA-512 hashes.'],
            ['name' => 'JWT Decoder', 'href' => '/jwt-decoder', 'category' => 'Security', 'from' => 'from-indigo-500', 'to' => 'to-blue-500', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'description' => 'Decode a JSON Web Token header and payload.'],
            ['name' => 'JWT Generator', 'href' => '/jwt-generator', 'category' => 'Security', 'from' => 'from-blue-500', 'to' => 'to-indigo-500', 'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'description' => 'Sign HS256/384/512 JSON Web Tokens in the browser.'],
            ['name' => 'Password Strength', 'href' => '/password-strength', 'category' => 'Security', 'from' => 'from-rose-500', 'to' => 'to-pink-500', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'description' => 'Check password strength, entropy and crack time.'],

            // ---- Design ----
            ['name' => 'Color Palette', 'href' => '/color-palette', 'category' => 'Design', 'from' => 'from-fuchsia-500', 'to' => 'to-pink-500', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'description' => 'Preview color codes and compare how they pair together.'],
            ['name' => 'Color Picker', 'href' => '/color-picker', 'category' => 'Design', 'from' => 'from-pink-500', 'to' => 'to-rose-500', 'icon' => 'M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z', 'description' => 'Pick colors, sample from screen and export formats.'],
            ['name' => 'Contrast Checker', 'href' => '/contrast-checker', 'category' => 'Design', 'from' => 'from-purple-500', 'to' => 'to-fuchsia-500', 'icon' => 'M12 3v18m0-18a9 9 0 000 18 9 9 0 000-18z', 'description' => 'Check WCAG colour contrast ratios for accessibility.'],
            ['name' => 'Gradient Generator', 'href' => '/gradient-generator', 'category' => 'Design', 'from' => 'from-pink-500', 'to' => 'to-violet-500', 'icon' => 'M4 4h16v16H4V4z M4 12h16', 'description' => 'Design CSS gradients and copy the generated code.'],
            ['name' => 'Box Shadow Generator', 'href' => '/box-shadow', 'category' => 'Design', 'from' => 'from-violet-500', 'to' => 'to-purple-500', 'icon' => 'M7 7h10v10H7V7z M11 11h6v6h-6', 'description' => 'Craft CSS box-shadows with a live preview.'],

            // ---- Image ----
            ['name' => 'Image Compressor', 'href' => '/image-compressor', 'category' => 'Image', 'from' => 'from-blue-500', 'to' => 'to-cyan-500', 'icon' => 'M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12', 'description' => 'Shrink JPG, PNG and WebP images without losing quality.'],
            ['name' => 'Image Cropper', 'href' => '/image-cropper', 'category' => 'Image', 'from' => 'from-orange-500', 'to' => 'to-amber-500', 'icon' => 'M3 4h3M3 4v3m18-3h-3m3 0v3M3 20h3m-3 0v-3m18 3h-3m3 0v-3M8 8h8v8H8z', 'description' => 'Crop, rotate and flip images and export to any format.'],
            ['name' => 'Image to SVG', 'href' => '/png-to-svg', 'category' => 'Image', 'from' => 'from-green-500', 'to' => 'to-emerald-500', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'description' => 'Trace raster images into scalable vector graphics.'],
            ['name' => 'SVG Optimizer', 'href' => '/svg-optimizer', 'category' => 'Image', 'from' => 'from-emerald-500', 'to' => 'to-green-500', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'description' => 'Clean and minify SVG files to reduce their size.'],
            ['name' => 'EXIF Viewer', 'href' => '/exif-viewer', 'category' => 'Image', 'from' => 'from-teal-500', 'to' => 'to-cyan-500', 'icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z', 'description' => 'Inspect image EXIF metadata, camera and GPS data.'],
            ['name' => 'Favicon Generator', 'href' => '/favicon-generator', 'category' => 'Image', 'from' => 'from-cyan-500', 'to' => 'to-blue-500', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'description' => 'Generate favicons in every size from one image.'],

            // ---- Document ----
            ['name' => 'Images to PDF', 'href' => '/images-to-pdf', 'category' => 'Document', 'from' => 'from-red-500', 'to' => 'to-orange-500', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'description' => 'Combine multiple images into a single downloadable PDF.'],
            ['name' => 'PDF Compressor', 'href' => '/pdf-compressor', 'category' => 'Document', 'from' => 'from-rose-500', 'to' => 'to-pink-500', 'icon' => 'M9 12h6m-3-3v6m-7 5h10a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'description' => 'Shrink PDF file size by re-rendering pages.'],
            ['name' => 'Word to PDF', 'href' => '/word-to-pdf', 'category' => 'Document', 'from' => 'from-blue-600', 'to' => 'to-sky-500', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'description' => 'Convert Word .docx documents into a formatted PDF.'],
            ['name' => 'PDF to Word', 'href' => '/pdf-to-word', 'category' => 'Document', 'from' => 'from-sky-500', 'to' => 'to-blue-600', 'icon' => 'M12 9v6m3-3H9m-4 9h14a2 2 0 002-2V7.414a1 1 0 00-.293-.707l-3.414-3.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'description' => 'Extract text from a PDF into an editable Word document.'],
            ['name' => 'PDF Merge', 'href' => '/pdf-merge', 'category' => 'Document', 'from' => 'from-rose-500', 'to' => 'to-red-500', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'description' => 'Combine several PDF files into one document.'],
            ['name' => 'PDF Split', 'href' => '/pdf-split', 'category' => 'Document', 'from' => 'from-red-500', 'to' => 'to-orange-500', 'icon' => 'M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5', 'description' => 'Extract, remove or split pages from a PDF.'],
            ['name' => 'PDF to Images', 'href' => '/pdf-to-images', 'category' => 'Document', 'from' => 'from-orange-500', 'to' => 'to-amber-500', 'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'description' => 'Convert each PDF page into a PNG or JPG image.'],
            ['name' => 'Markdown to PDF', 'href' => '/markdown-to-pdf', 'category' => 'Document', 'from' => 'from-cyan-500', 'to' => 'to-sky-500', 'icon' => 'M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1zm2 10V9l3 3 3-3v6m3 0l2-2m0 0l-2-2m2 2h-4', 'description' => 'Render Markdown into a clean, paginated PDF.'],
            ['name' => 'HTML to PDF', 'href' => '/html-to-pdf', 'category' => 'Document', 'from' => 'from-indigo-500', 'to' => 'to-violet-500', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'description' => 'Turn raw HTML markup into a downloadable PDF.'],
        ];
    }

    // =====================================================================
    //  VAS / DCB MODULE (detachable) — delete this method and its call in
    //  boot() to remove the whole section from the dashboard.
    // =====================================================================
    /**
     * @return array<int, array<string, string>>
     */
    private function vasQuickies(): array
    {
        $cat = 'VAS & DCB';

        return [
            ['name' => 'ROI / EPC / CPA Calculator', 'href' => '/vas/roi-calculator', 'category' => $cat, 'from' => 'from-emerald-500', 'to' => 'to-green-500', 'icon' => 'M3 3v18h18M7 14l3-3 4 4 5-6', 'description' => 'Calculate ROI, EPC, CPA, ARPU and profit margins for campaigns.'],
            ['name' => 'MSISDN Validator', 'href' => '/vas/msisdn', 'category' => $cat, 'from' => 'from-blue-500', 'to' => 'to-indigo-500', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11 11 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'description' => 'Validate and format phone numbers to E.164 with carrier hints.'],
            ['name' => 'UTM Builder', 'href' => '/vas/utm-builder', 'category' => $cat, 'from' => 'from-violet-500', 'to' => 'to-purple-500', 'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'description' => 'Build trackable campaign URLs with UTM parameters.'],
            ['name' => 'SMS Encoding Checker', 'href' => '/vas/sms-checker', 'category' => $cat, 'from' => 'from-cyan-500', 'to' => 'to-blue-500', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4v-4z', 'description' => 'Count SMS segments and detect GSM-7 vs UCS-2 encoding.'],
            ['name' => 'MCC / MNC Lookup', 'href' => '/vas/mcc-mnc', 'category' => $cat, 'from' => 'from-teal-500', 'to' => 'to-cyan-500', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'description' => 'Look up mobile carriers by MCC/MNC and country.'],
            ['name' => 'Postback Builder', 'href' => '/vas/postback', 'category' => $cat, 'from' => 'from-amber-500', 'to' => 'to-orange-500', 'icon' => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15', 'description' => 'Build and fire S2S postback URLs with macro placeholders.'],
            ['name' => 'Webhook Inspector', 'href' => '/vas/webhook-inspector', 'category' => $cat, 'from' => 'from-orange-500', 'to' => 'to-red-500', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'description' => 'Inspect and pretty-print incoming webhook payloads.'],
            ['name' => 'Redirect Checker', 'href' => '/vas/redirect-checker', 'category' => $cat, 'from' => 'from-red-500', 'to' => 'to-rose-500', 'icon' => 'M14 5l7 7m0 0l-7 7m7-7H3', 'description' => 'Trace redirect chains and inspect each hop of a URL.'],
            ['name' => 'A/B Significance', 'href' => '/vas/ab-test', 'category' => $cat, 'from' => 'from-fuchsia-500', 'to' => 'to-pink-500', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'description' => 'Test conversion-rate differences for statistical significance.'],
            ['name' => 'JSON ↔ Excel ↔ CSV', 'href' => '/vas/json-excel-csv', 'category' => $cat, 'from' => 'from-green-500', 'to' => 'to-emerald-500', 'icon' => 'M3 10h18M3 14h18M12 4v16M4 4h16a1 1 0 011 1v14a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z', 'description' => 'Convert data between JSON, Excel (XLSX) and CSV.'],
            ['name' => 'REST API Tester', 'href' => '/vas/api-tester', 'category' => $cat, 'from' => 'from-indigo-500', 'to' => 'to-blue-500', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4', 'description' => 'Send HTTP requests and inspect the response like Postman.'],
            ['name' => 'SQL Query Generator', 'href' => '/vas/sql-query-generator', 'category' => $cat, 'from' => 'from-purple-500', 'to' => 'to-violet-500', 'icon' => 'M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3-3.582 3-8 3-8-1.343-8-3zm0 0v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7', 'description' => 'Build and format SELECT, INSERT and UPDATE SQL visually.'],
            ['name' => 'Ad / SMS / Push Generator', 'href' => '/vas/ad-generator', 'category' => $cat, 'from' => 'from-pink-500', 'to' => 'to-rose-500', 'icon' => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z', 'description' => 'Generate ad copy, SMS and push notification variants.'],
            ['name' => 'Revenue & LTV Calculator', 'href' => '/vas/ltv-calculator', 'category' => $cat, 'from' => 'from-emerald-500', 'to' => 'to-teal-500', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'description' => 'Model subscription revenue, churn, LTV and payback.'],
            ['name' => 'Deep Link & QR', 'href' => '/vas/deep-link-qr', 'category' => $cat, 'from' => 'from-blue-500', 'to' => 'to-cyan-500', 'icon' => 'M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 4v-4m0 4h2m4 0v-4m0 0h-4m4 0h.01M14 14h.01', 'description' => 'Build deep links with UTM tags and matching QR codes.'],
        ];
    }
}
