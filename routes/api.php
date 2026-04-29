<?php

use App\Http\Controllers\Api\AmenidadController;
use App\Http\Controllers\Api\CuponController;
use App\Http\Controllers\Api\EventoController;
use App\Http\Controllers\Api\HistoriaController;
use App\Http\Controllers\Api\NoticiaController;
use App\Http\Controllers\Api\TimelineController;
use App\Http\Controllers\Api\Auth\CommerceAdminAuthController;
use App\Http\Controllers\Api\Auth\CommerceCouponController;
use App\Http\Controllers\Api\Auth\CommerceGalleryController;
use App\Http\Controllers\Api\Auth\CommerceRegistrationController;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\Auth\UserCouponController;
use App\Http\Controllers\Api\EstablecimientoController;
use App\Http\Controllers\Api\Integration\ApprovedPreregisterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\TipoController;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/roles', [RoleController::class, 'index']);
Route::get('/tipos', [TipoController::class, 'index']);
Route::get('/amenidades', [AmenidadController::class, 'index']);
Route::get('/eventos', [EventoController::class, 'index']);
Route::get('/eventos/categorias', [EventoController::class, 'categorias']);
Route::get('/eventos/{evento}', [EventoController::class, 'show']);
Route::post('/eventos/{evento}/asistire', [EventoController::class, 'asistire']);
Route::get('/historias', [HistoriaController::class, 'index']);
Route::get('/historias/{id}', [HistoriaController::class, 'show']);
Route::get('/noticias', [NoticiaController::class, 'index']);
Route::get('/noticias/{noticia}', [NoticiaController::class, 'show']);
Route::get('/timelines', [TimelineController::class, 'index']);
Route::get('/timelines/{id}', [TimelineController::class, 'show']);
Route::get('/tipo-documentos', [TipoDocumentoController::class, 'index']);
Route::get('/establecimientos', [EstablecimientoController::class, 'index']);
Route::get('/establecimientos/{id}', [EstablecimientoController::class, 'show']);
Route::get('/cupones', [CuponController::class, 'index']);
Route::post('/integracion/preregistros/aprobar', [ApprovedPreregisterController::class, 'store']);

Route::prefix('auth/comercios')->group(function () {
    Route::post('/login', [CommerceAdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [CommerceAdminAuthController::class, 'me']);
        Route::post('/logout', [CommerceAdminAuthController::class, 'logout']);
        Route::put('/profile', [CommerceAdminAuthController::class, 'updateProfile']);
        Route::post('/user-profile', [CommerceAdminAuthController::class, 'updateUserProfile']);
        Route::put('/visibility', [CommerceAdminAuthController::class, 'updateVisibility']);
        Route::post('/registro-establecimiento', [CommerceRegistrationController::class, 'save']);
        Route::get('/cupones', [CommerceCouponController::class, 'index']);
        Route::get('/cupones/{cuponId}/redenciones', [CommerceCouponController::class, 'redemptions']);
        Route::post('/cupones', [CommerceCouponController::class, 'store']);
        Route::put('/cupones/{cuponId}', [CommerceCouponController::class, 'update']);
        Route::post('/cupones/redimir', [CommerceCouponController::class, 'redeemByCode']);
        Route::post('/galeria/fotos', [CommerceGalleryController::class, 'store']);
        Route::post('/galeria/fotos/{documentoId}/replace', [CommerceGalleryController::class, 'replace']);
        Route::delete('/galeria/fotos/{documentoId}', [CommerceGalleryController::class, 'destroy']);
    });
});

// Registro de usuarios
Route::prefix('auth/usuarios')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login']);
    Route::post('/registro', [RegisterController::class, 'register']);
    Route::get('/activar/{token}', [RegisterController::class, 'activate']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [UserAuthController::class, 'me']);
        Route::post('/profile', [UserAuthController::class, 'updateProfile']);
        Route::post('/logout', [UserAuthController::class, 'logout']);
        Route::get('/cupones', [UserCouponController::class, 'index']);
        Route::post('/cupones/{cuponId}/guardar', [UserCouponController::class, 'store']);
    });
});

// Alias temporal para compatibilidad con clientes existentes
Route::post('/auth/registro', [RegisterController::class, 'register']);
