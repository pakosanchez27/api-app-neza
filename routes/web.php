<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AprobarComerciosController;
use App\Http\Controllers\CatalogosController;
use App\Http\Controllers\ComerciosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\PuntosMapaController;
use App\Http\Controllers\TimelineModelController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

if ($adminDomain = env('ADMIN_APP_DOMAIN')) {
    Route::domain($adminDomain)->group(function () {
        Route::get('/', function (Request $request) {
            if (
                filter_var(env('ADMIN_AUTH_BYPASS', false), FILTER_VALIDATE_BOOL)
                && ! $request->session()->get('admin_bypass_logged_out')
            ) {
                return redirect()->route('admin.dashboard');
            }

            $isAuthenticated = (bool) $request->session()->get('admin_auth');
            $token = $request->session()->get('admin_access_token');
            $user = $request->session()->get('admin_user', []);
            $isActive = (bool) ($user['activo'] ?? true);

            if ($isAuthenticated && ! empty($token) && ! empty($user) && $isActive) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('admin.login');
        })->name('admin.entry');
    });
}

Route::view('/', 'landing')->name('landing');

Route::view('/docs/api', 'api-docs')->name('api.docs');

Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');

Route::middleware(['admin.app'])->group(function () {
    Route::get('/admin', [HomeController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/historia', [HistoriaController::class, 'index'])->name('admin.historia');
    Route::get('/admin/historia/create', [HistoriaController::class, 'create'])->name('admin.historia.create');
    Route::post('/admin/historia/store', [HistoriaController::class, 'store'])->name('admin.historia.store');
    Route::get('/admin/historia/{historia}/edit', [HistoriaController::class, 'edit'])->name('admin.historia.edit');
    Route::put('/admin/historia/{historia}', [HistoriaController::class, 'update'])->name('admin.historia.update');
    Route::patch('/admin/historia/{historia}/activate', [HistoriaController::class, 'activate'])->name('admin.historia.activate');
    Route::delete('/admin/historia/{historia}', [HistoriaController::class, 'destroy'])->name('admin.historia.destroy');

    // Noticias
    Route::get('/admin/noticias', [NoticiasController::class, 'index'])->name('admin.noticias');
    Route::get('/admin/noticias/create', [NoticiasController::class, 'create'])->name('admin.noticias.create');
    Route::post('/admin/noticias/store', [NoticiasController::class, 'store'])->name('admin.noticias.store');
    Route::get('/admin/noticias/{noticia}/edit', [NoticiasController::class, 'edit'])->name('admin.noticias.edit');
    Route::put('/admin/noticias/{noticia}', [NoticiasController::class, 'update'])->name('admin.noticias.update');
    Route::delete('/admin/noticias/{noticia}', [NoticiasController::class, 'destroy'])->name('admin.noticias.destroy');

    // Eventos
    Route::get('/admin/eventos', [EventosController::class, 'index'])->name('admin.eventos');
    Route::get('/admin/eventos/create', [EventosController::class, 'create'])->name('admin.eventos.create');
    Route::get('/admin/eventos/catalogo/calles', [EventosController::class, 'callesCatalogo'])->name('admin.eventos.catalogo.calles');
    Route::get('/admin/eventos/catalogo/direccion', [EventosController::class, 'buscarCoordenadasPorDireccion'])->name('admin.eventos.catalogo.direccion');
    Route::post('/admin/eventos/store', [EventosController::class, 'store'])->name('admin.eventos.store');
    Route::get('/admin/eventos/{evento}/edit', [EventosController::class, 'edit'])->name('admin.eventos.edit');
    Route::put('/admin/eventos/{evento}', [EventosController::class, 'update'])->name('admin.eventos.update');
    Route::delete('/admin/eventos/{evento}', [EventosController::class, 'destroy'])->name('admin.eventos.destroy');

    // Timeline hitoria
    Route::get('/admin/timeline', [TimelineModelController::class, 'index'])->name('admin.timeline');
    Route::get('/admin/timeline/create', [TimelineModelController::class, 'create'])->name('admin.timeline.create');
    Route::post('/admin/timeline/store', [TimelineModelController::class, 'store'])->name('admin.timeline.store');
    Route::get('/admin/timeline/{timeline}/edit', [TimelineModelController::class, 'edit'])->name('admin.timeline.edit');
    Route::put('/admin/timeline/{timeline}', [TimelineModelController::class, 'update'])->name('admin.timeline.update');
    Route::delete('/admin/timeline/{timeline}', [TimelineModelController::class, 'destroy'])->name('admin.timeline.destroy');

    Route::get('/admin/usuarios', [UsuariosController::class, 'index'])->name('admin.usuarios');
    Route::get('/admin/usuarios/{user}/edit', [UsuariosController::class, 'edit'])->name('admin.usuarios.edit');
    Route::put('/admin/usuarios/{user}', [UsuariosController::class, 'update'])->name('admin.usuarios.update');
    Route::patch('/admin/usuarios/{user}/toggle-status', [UsuariosController::class, 'toggleStatus'])->name('admin.usuarios.toggle-status');
    Route::post('/admin/usuarios/{user}/reset-password', [UsuariosController::class, 'sendResetPassword'])->name('admin.usuarios.reset-password');

    Route::get('/admin/comercios', [ComerciosController::class, 'index'])->name('admin.comercios');
    Route::get('/admin/comercios/{user}', [ComerciosController::class, 'show'])->name('admin.comercios.show');
    Route::get('/admin/comercios/{user}/edit', [ComerciosController::class, 'edit'])->name('admin.comercios.edit');
    Route::put('/admin/comercios/{user}', [ComerciosController::class, 'update'])->name('admin.comercios.update');
    Route::patch('/admin/comercios/{user}/toggle-status', [ComerciosController::class, 'toggleStatus'])->name('admin.comercios.toggle-status');
    Route::post('/admin/comercios/{user}/reset-password', [ComerciosController::class, 'sendResetPassword'])->name('admin.comercios.reset-password');

    Route::get('/admin/catalogos/tipos-negocio', [CatalogosController::class, 'tiposNegocio'])->name('admin.catalogos.tipos-negocio');
    Route::get('/admin/catalogos/tipos-negocio/create', [CatalogosController::class, 'createTipoNegocio'])->name('admin.catalogos.tipos-negocio.create');
    Route::post('/admin/catalogos/tipos-negocio', [CatalogosController::class, 'storeTipoNegocio'])->name('admin.catalogos.tipos-negocio.store');
    Route::get('/admin/catalogos/tipos-negocio/{tipo}/edit', [CatalogosController::class, 'editTipoNegocio'])->name('admin.catalogos.tipos-negocio.edit');
    Route::put('/admin/catalogos/tipos-negocio/{tipo}', [CatalogosController::class, 'updateTipoNegocio'])->name('admin.catalogos.tipos-negocio.update');
    Route::delete('/admin/catalogos/tipos-negocio/{tipo}', [CatalogosController::class, 'destroyTipoNegocio'])->name('admin.catalogos.tipos-negocio.destroy');

    Route::get('/admin/catalogos/categorias-eventos', [CatalogosController::class, 'categoriasEventos'])->name('admin.catalogos.categorias-eventos');
    Route::get('/admin/catalogos/categorias-eventos/create', [CatalogosController::class, 'createCategoriaEvento'])->name('admin.catalogos.categorias-eventos.create');
    Route::post('/admin/catalogos/categorias-eventos', [CatalogosController::class, 'storeCategoriaEvento'])->name('admin.catalogos.categorias-eventos.store');
    Route::get('/admin/catalogos/categorias-eventos/{categoria}/edit', [CatalogosController::class, 'editCategoriaEvento'])->name('admin.catalogos.categorias-eventos.edit');
    Route::put('/admin/catalogos/categorias-eventos/{categoria}', [CatalogosController::class, 'updateCategoriaEvento'])->name('admin.catalogos.categorias-eventos.update');
    Route::delete('/admin/catalogos/categorias-eventos/{categoria}', [CatalogosController::class, 'destroyCategoriaEvento'])->name('admin.catalogos.categorias-eventos.destroy');

    Route::get('/admin/catalogos/categorias-mapa', [CatalogosController::class, 'categoriasMapa'])->name('admin.catalogos.categorias-mapa');
    Route::get('/admin/catalogos/categorias-mapa/create', [CatalogosController::class, 'createCategoriaMapa'])->name('admin.catalogos.categorias-mapa.create');
    Route::post('/admin/catalogos/categorias-mapa', [CatalogosController::class, 'storeCategoriaMapa'])->name('admin.catalogos.categorias-mapa.store');
    Route::get('/admin/catalogos/categorias-mapa/{categoria}/edit', [CatalogosController::class, 'editCategoriaMapa'])->name('admin.catalogos.categorias-mapa.edit');
    Route::put('/admin/catalogos/categorias-mapa/{categoria}', [CatalogosController::class, 'updateCategoriaMapa'])->name('admin.catalogos.categorias-mapa.update');
    Route::delete('/admin/catalogos/categorias-mapa/{categoria}', [CatalogosController::class, 'destroyCategoriaMapa'])->name('admin.catalogos.categorias-mapa.destroy');

    Route::get('/admin/aprobar-comercios', [AprobarComerciosController::class, 'index'])->name('admin.aprobar-comercios');
    Route::patch('/admin/aprobar-comercios/{preregistro}/approve', [AprobarComerciosController::class, 'approve'])->name('admin.aprobar-comercios.approve');
    Route::patch('/admin/aprobar-comercios/{preregistro}/correction', [AprobarComerciosController::class, 'requestCorrection'])->name('admin.aprobar-comercios.correction');
    Route::patch('/admin/aprobar-comercios/{preregistro}/reject', [AprobarComerciosController::class, 'reject'])->name('admin.aprobar-comercios.reject');

    Route::get('/admin/puntos-mapa', [PuntosMapaController::class, 'index'])->name('admin.puntos-mapa');
    Route::get('/admin/puntos-mapa/create', [PuntosMapaController::class, 'create'])->name('admin.puntos-mapa.create');
    Route::post('/admin/puntos-mapa/store', [PuntosMapaController::class, 'store'])->name('admin.puntos-mapa.store');
    Route::get('/admin/puntos-mapa/{puntoMapa}/edit', [PuntosMapaController::class, 'edit'])->name('admin.puntos-mapa.edit');
    Route::put('/admin/puntos-mapa/{puntoMapa}', [PuntosMapaController::class, 'update'])->name('admin.puntos-mapa.update');
    Route::delete('/admin/puntos-mapa/{puntoMapa}', [PuntosMapaController::class, 'destroy'])->name('admin.puntos-mapa.destroy');
    Route::get('/admin/puntos-mapa/{puntoMapa}/foto', [PuntosMapaController::class, 'fotoPrincipal'])->name('admin.puntos-mapa.foto');
});
