<?php

use Illuminate\Support\Facades\Route;

// Marketing / auth / billing / admin
use App\Http\Controllers\PrelanderController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\UpgradeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Billing\CheckoutController;
use App\Http\Controllers\Billing\WebhookController;
use App\Http\Controllers\Admin\AdminController;

// Tool controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColorPaletteController;
use App\Http\Controllers\PngToSvgController;
use App\Http\Controllers\ImageCompressorController;
use App\Http\Controllers\ImageCropperController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\PasswordGeneratorController;
use App\Http\Controllers\HashGeneratorController;
use App\Http\Controllers\Base64Controller;
use App\Http\Controllers\JsonFormatterController;
use App\Http\Controllers\WordCounterController;
use App\Http\Controllers\UuidGeneratorController;
use App\Http\Controllers\LoremIpsumController;
use App\Http\Controllers\ColorPickerController;
use App\Http\Controllers\ImagesToPdfController;
use App\Http\Controllers\PdfCompressorController;
use App\Http\Controllers\WordToPdfController;
use App\Http\Controllers\PdfToWordController;
use App\Http\Controllers\DiffCheckerController;
use App\Http\Controllers\RegexTesterController;
use App\Http\Controllers\TimestampController;
use App\Http\Controllers\JwtDecoderController;
use App\Http\Controllers\MarkdownController;
use App\Http\Controllers\CaseConverterController;
use App\Http\Controllers\RemoveDuplicatesController;
use App\Http\Controllers\SortLinesController;
use App\Http\Controllers\SlugController;
use App\Http\Controllers\JsonYamlController;
use App\Http\Controllers\CsvJsonController;
use App\Http\Controllers\BaseNController;
use App\Http\Controllers\JsonDiffController;
use App\Http\Controllers\SqlFormatterController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\JsonSchemaController;
use App\Http\Controllers\DockerComposeController;
use App\Http\Controllers\TokenCounterController;
use App\Http\Controllers\UrlEncoderController;
use App\Http\Controllers\QueryParserController;
use App\Http\Controllers\CurlConverterController;
use App\Http\Controllers\HttpStatusController;
use App\Http\Controllers\TimezoneController;
use App\Http\Controllers\BusinessDaysController;
use App\Http\Controllers\IdGeneratorController;
use App\Http\Controllers\MockJsonController;
use App\Http\Controllers\JwtGeneratorController;
use App\Http\Controllers\PasswordStrengthController;
use App\Http\Controllers\ContrastCheckerController;
use App\Http\Controllers\GradientGeneratorController;
use App\Http\Controllers\BoxShadowController;
use App\Http\Controllers\ExifViewerController;
use App\Http\Controllers\FaviconGeneratorController;
use App\Http\Controllers\SvgOptimizerController;
use App\Http\Controllers\PdfMergeController;
use App\Http\Controllers\PdfSplitController;
use App\Http\Controllers\PdfToImagesController;
use App\Http\Controllers\MarkdownToPdfController;
use App\Http\Controllers\HtmlToPdfController;

/*
|--------------------------------------------------------------------------
| Public marketing
|--------------------------------------------------------------------------
*/
Route::get('/', [PrelanderController::class, 'index'])->name('home');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing');
Route::get('/upgrade', [UpgradeController::class, 'show'])->name('upgrade');

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Billing (Stripe)
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [WebhookController::class, 'handle'])->name('stripe.webhook');
Route::middleware('auth')->group(function () {
    Route::post('/billing/checkout/{plan}', [CheckoutController::class, 'checkout'])->name('billing.checkout');
    Route::get('/billing/success', [CheckoutController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal', [CheckoutController::class, 'portal'])->name('billing.portal');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
});

/*
|--------------------------------------------------------------------------
| Tools dashboard (public to browse)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Tool pages — gated by subscription / free daily limits
|--------------------------------------------------------------------------
*/
Route::middleware('tool.gate')->group(function () {
    Route::get('/color-palette', [ColorPaletteController::class, 'index']);
    Route::get('/png-to-svg', [PngToSvgController::class, 'index']);
    Route::get('/image-compressor', [ImageCompressorController::class, 'index']);
    Route::get('/image-cropper', [ImageCropperController::class, 'index']);
    Route::get('/qr-code', [QrCodeController::class, 'index']);
    Route::get('/password-generator', [PasswordGeneratorController::class, 'index']);
    Route::get('/hash-generator', [HashGeneratorController::class, 'index']);
    Route::get('/base64', [Base64Controller::class, 'index']);
    Route::get('/json-formatter', [JsonFormatterController::class, 'index']);
    Route::get('/word-counter', [WordCounterController::class, 'index']);
    Route::get('/uuid-generator', [UuidGeneratorController::class, 'index']);
    Route::get('/lorem-ipsum', [LoremIpsumController::class, 'index']);
    Route::get('/color-picker', [ColorPickerController::class, 'index']);
    Route::get('/images-to-pdf', [ImagesToPdfController::class, 'index']);
    Route::get('/pdf-compressor', [PdfCompressorController::class, 'index']);
    Route::get('/word-to-pdf', [WordToPdfController::class, 'index']);
    Route::get('/pdf-to-word', [PdfToWordController::class, 'index']);
    Route::get('/diff-checker', [DiffCheckerController::class, 'index']);
    Route::get('/regex-tester', [RegexTesterController::class, 'index']);
    Route::get('/timestamp', [TimestampController::class, 'index']);
    Route::get('/jwt-decoder', [JwtDecoderController::class, 'index']);
    Route::get('/markdown', [MarkdownController::class, 'index']);
    Route::get('/case-converter', [CaseConverterController::class, 'index']);
    Route::get('/remove-duplicates', [RemoveDuplicatesController::class, 'index']);
    Route::get('/sort-lines', [SortLinesController::class, 'index']);
    Route::get('/slug', [SlugController::class, 'index']);
    Route::get('/json-yaml', [JsonYamlController::class, 'index']);
    Route::get('/csv-json', [CsvJsonController::class, 'index']);
    Route::get('/base-n', [BaseNController::class, 'index']);
    Route::get('/json-diff', [JsonDiffController::class, 'index']);
    Route::get('/sql-formatter', [SqlFormatterController::class, 'index']);
    Route::get('/cron', [CronController::class, 'index']);
    Route::get('/json-schema', [JsonSchemaController::class, 'index']);
    Route::get('/docker-compose', [DockerComposeController::class, 'index']);
    Route::get('/token-counter', [TokenCounterController::class, 'index']);
    Route::get('/url-encoder', [UrlEncoderController::class, 'index']);
    Route::get('/query-parser', [QueryParserController::class, 'index']);
    Route::get('/curl-converter', [CurlConverterController::class, 'index']);
    Route::get('/http-status', [HttpStatusController::class, 'index']);
    Route::get('/timezone', [TimezoneController::class, 'index']);
    Route::get('/business-days', [BusinessDaysController::class, 'index']);
    Route::get('/id-generator', [IdGeneratorController::class, 'index']);
    Route::get('/mock-json', [MockJsonController::class, 'index']);
    Route::get('/jwt-generator', [JwtGeneratorController::class, 'index']);
    Route::get('/password-strength', [PasswordStrengthController::class, 'index']);
    Route::get('/contrast-checker', [ContrastCheckerController::class, 'index']);
    Route::get('/gradient-generator', [GradientGeneratorController::class, 'index']);
    Route::get('/box-shadow', [BoxShadowController::class, 'index']);
    Route::get('/exif-viewer', [ExifViewerController::class, 'index']);
    Route::get('/favicon-generator', [FaviconGeneratorController::class, 'index']);
    Route::get('/svg-optimizer', [SvgOptimizerController::class, 'index']);
    Route::get('/pdf-merge', [PdfMergeController::class, 'index']);
    Route::get('/pdf-split', [PdfSplitController::class, 'index']);
    Route::get('/pdf-to-images', [PdfToImagesController::class, 'index']);
    Route::get('/markdown-to-pdf', [MarkdownToPdfController::class, 'index']);
    Route::get('/html-to-pdf', [HtmlToPdfController::class, 'index']);

    // ========================================================================
    //  VAS / DCB MODULE (detachable) — remove this whole block, the
    //  App\Http\Controllers\Vas namespace and resources/views/quickies/vas to
    //  fully detach the section.
    // ========================================================================
    Route::prefix('vas')->group(function () {
        Route::get('/roi-calculator', [\App\Http\Controllers\Vas\RoiCalculatorController::class, 'index']);
        Route::get('/msisdn', [\App\Http\Controllers\Vas\MsisdnController::class, 'index']);
        Route::get('/utm-builder', [\App\Http\Controllers\Vas\UtmBuilderController::class, 'index']);
        Route::get('/sms-checker', [\App\Http\Controllers\Vas\SmsCheckerController::class, 'index']);
        Route::get('/mcc-mnc', [\App\Http\Controllers\Vas\MccMncController::class, 'index']);
        Route::get('/postback', [\App\Http\Controllers\Vas\PostbackController::class, 'index']);
        Route::get('/webhook-inspector', [\App\Http\Controllers\Vas\WebhookInspectorController::class, 'index']);
        Route::get('/redirect-checker', [\App\Http\Controllers\Vas\RedirectCheckerController::class, 'index']);
        Route::get('/ab-test', [\App\Http\Controllers\Vas\AbTestController::class, 'index']);
        Route::get('/json-excel-csv', [\App\Http\Controllers\Vas\JsonExcelCsvController::class, 'index']);
        Route::get('/api-tester', [\App\Http\Controllers\Vas\ApiTesterController::class, 'index']);
        Route::get('/sql-query-generator', [\App\Http\Controllers\Vas\SqlQueryGeneratorController::class, 'index']);
        Route::get('/ad-generator', [\App\Http\Controllers\Vas\AdGeneratorController::class, 'index']);
        Route::get('/ltv-calculator', [\App\Http\Controllers\Vas\LtvCalculatorController::class, 'index']);
        Route::get('/deep-link-qr', [\App\Http\Controllers\Vas\DeepLinkQrController::class, 'index']);
    });
    // ========================== END VAS / DCB MODULE =========================
});
