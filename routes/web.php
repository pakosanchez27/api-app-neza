<?php

use App\Http\Controllers\EventosController;
use App\Http\Controllers\HistoriaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\TimelineModelController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing')->name('landing');

Route::view('/docs/api', 'api-docs')->name('api.docs');

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
