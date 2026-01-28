<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ColorPaletteController;
use App\Http\Controllers\PngToSvgController;
use App\Http\Controllers\ImageCompressorController;
use App\Http\Controllers\ImageCropperController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/color-palette', [ColorPaletteController::class, 'index']);
Route::get('/png-to-svg', [PngToSvgController::class, 'index']);
Route::get('/image-compressor', [ImageCompressorController::class, 'index']);
Route::get('/image-cropper', [ImageCropperController::class, 'index']);
