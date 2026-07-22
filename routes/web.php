<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DragonController;
use App\Http\Controllers\DragonOwningController;
use App\Http\Controllers\DragonOwningDetailController;
use App\Http\Controllers\ElementController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RarityController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dragons/scrape', [DragonController::class, 'scrape'])->name('dragons.scrape');
Route::post('/dragons/truncate', [DragonController::class, 'truncate'])->name('dragons.truncate');
Route::post('/dragons/generate-aliases', [DragonController::class, 'generateAliases'])->name('dragons.generate-aliases');
Route::post('/dragons/{dragon}/best-heroic', [DragonController::class, 'markBestHeroic'])->name('dragons.markBestHeroic');
Route::resource('accounts', AccountController::class);
Route::resource('elements', ElementController::class);
Route::resource('rarities', RarityController::class);
Route::resource('dragons', DragonController::class);

Route::get('/dragon-ownings', [DragonOwningController::class, 'index'])->name('dragon-ownings.index');
Route::get('/dragon-ownings/{account}/create', [DragonOwningController::class, 'create'])->name('dragon-ownings.create');
Route::post('/dragon-owning-details/{account}', [DragonOwningDetailController::class, 'store'])->name('dragon-owning-details.store');
Route::delete('/dragon-owning-details/{account}/{dragonOwningDetail}', [DragonOwningDetailController::class, 'destroy'])->name('dragon-owning-details.destroy');

Route::get('/orb-ownings', [App\Http\Controllers\OrbOwningController::class, 'index'])->name('orb-ownings.index');
Route::get('/orb-ownings/create', [App\Http\Controllers\OrbOwningController::class, 'create'])->name('orb-ownings.create');
Route::post('/orb-ownings', [App\Http\Controllers\OrbOwningController::class, 'store'])->name('orb-ownings.store');
Route::get('/orb-ownings/{orbOwning}/edit', [App\Http\Controllers\OrbOwningController::class, 'edit'])->name('orb-ownings.edit');
Route::put('/orb-ownings/{orbOwning}', [App\Http\Controllers\OrbOwningController::class, 'update'])->name('orb-ownings.update');
Route::delete('/orb-ownings/{orbOwning}', [App\Http\Controllers\OrbOwningController::class, 'destroy'])->name('orb-ownings.destroy');
