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
Route::post('/home/run-seeder', [HomeController::class, 'runSeeder'])->name('home.run-seeder');

Route::get('/dragons/scrape', [DragonController::class, 'scrape'])->name('dragons.scrape');
Route::post('/dragons/truncate', [DragonController::class, 'truncate'])->name('dragons.truncate');
Route::post('/accounts/update-seeder', [AccountController::class, 'updateSeeder'])->name('accounts.update-seeder');
Route::post('/dragons/restore-latest', [DragonController::class, 'restoreLatest'])->name('dragons.restore-latest');
Route::post('/dragons/generate-aliases', [DragonController::class, 'generateAliases'])->name('dragons.generate-aliases');
Route::post('/dragons/export-seeder-array', [DragonController::class, 'exportSeederArray'])->name('dragons.export-seeder-array');
Route::post('/dragons/{dragon}/best-heroic', [DragonController::class, 'markBestHeroic'])->name('dragons.markBestHeroic');
Route::resource('accounts', AccountController::class);
Route::post('/elements/update-seeder', [ElementController::class, 'updateSeeder'])->name('elements.update-seeder');
Route::post('/rarities/update-seeder', [RarityController::class, 'updateSeeder'])->name('rarities.update-seeder');
Route::resource('elements', ElementController::class);
Route::resource('rarities', RarityController::class);
Route::resource('dragons', DragonController::class);
Route::get('/master-dragons', [DragonController::class, 'masterDragon'])->name('master-dragons.index');

Route::get('/dragon-ownings', [DragonOwningController::class, 'index'])->name('dragon-ownings.index');
Route::get('/dragon-ownings/{account}/create', [DragonOwningController::class, 'create'])->name('dragon-ownings.create');
Route::get('/dragon-ownings/{account}/search', [DragonOwningController::class, 'search'])->name('dragon-ownings.search');
Route::get('/dragon-ownings/{account}/data', [DragonOwningController::class, 'data'])->name('dragon-ownings.data');
Route::get('/dragon-ownings/{account}', [DragonOwningController::class, 'show'])->name('dragon-ownings.show');
Route::post('/dragon-owning-details/{account}', [DragonOwningDetailController::class, 'store'])->name('dragon-owning-details.store');
Route::delete('/dragon-owning-details/{account}/{dragonOwningDetail}', [DragonOwningDetailController::class, 'destroy'])->name('dragon-owning-details.destroy');

Route::get('/orb-ownings', [App\Http\Controllers\OrbOwningController::class, 'index'])->name('orb-ownings.index');
Route::get('/orb-ownings/create', [App\Http\Controllers\OrbOwningController::class, 'create'])->name('orb-ownings.create');
Route::post('/orb-ownings', [App\Http\Controllers\OrbOwningController::class, 'store'])->name('orb-ownings.store');
Route::post('/orb-ownings/best-heroic', [App\Http\Controllers\OrbOwningController::class, 'storeBestHeroic'])->name('orb-ownings.store-best-heroic');
Route::get('/orb-ownings/{orbOwning}/edit', [App\Http\Controllers\OrbOwningController::class, 'edit'])->name('orb-ownings.edit');
Route::put('/orb-ownings/{orbOwning}', [App\Http\Controllers\OrbOwningController::class, 'update'])->name('orb-ownings.update');
Route::delete('/orb-ownings/{orbOwning}', [App\Http\Controllers\OrbOwningController::class, 'destroy'])->name('orb-ownings.destroy');
Route::post('/orb-ownings/upsert', [App\Http\Controllers\OrbOwningController::class, 'upsert'])->name('orb-ownings.upsert');
