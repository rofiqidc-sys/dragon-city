<?php

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('migrate:new {--graceful}', function () {
    $migrationPath = database_path('migrations');
    $repository = $this->laravel->make('migration.repository');

    $files = collect(File::files($migrationPath))
        ->filter(fn ($file) => strtolower($file->getExtension()) === 'php')
        ->sortBy(fn ($file) => $file->getBasename('.php'));

    $pending = $files->reject(fn ($file) => in_array($file->getBasename('.php'), $repository->getRan()));

    if ($pending->isEmpty()) {
        $this->info('Nothing to migrate. All migrations are already up to date.');

        return;
    }

    $newest = $pending->last();
    $path = 'database/migrations/' . $newest->getFilename();

    $this->info('Running newest pending migration: ' . $newest->getFilename());

    $options = ['--path' => $path];
    if ($this->option('graceful')) {
        $options['--graceful'] = true;
    }

    $this->call('migrate', $options);
})->purpose('Run only the newest pending migration file');

Artisan::command('dragon:repair-rarity', function () {
    $mythicalElement = Element::whereRaw('LOWER(name) = ?', ['mythical'])->first();
    $heroicElement = Element::whereRaw('LOWER(name) = ?', ['heroic'])->first();

    $mythicalRarity = Rarity::firstOrCreate([
        'name' => 'Mythical',
    ], [
        'alias' => 'mythical',
    ]);

    $heroicRarity = Rarity::firstOrCreate([
        'name' => 'Heroic',
    ], [
        'alias' => 'heroic',
    ]);

    $elementIdMap = [];
    if ($mythicalElement) {
        $elementIdMap[$mythicalElement->id] = $mythicalRarity;
    }
    if ($heroicElement) {
        $elementIdMap[$heroicElement->id] = $heroicRarity;
    }

    if ($elementIdMap === []) {
        $this->info('No Mythical or Heroic elements found to repair.');

        return;
    }

    $fixed = 0;

    Dragon::where(function ($query) use ($elementIdMap) {
        foreach (array_keys($elementIdMap) as $elementId) {
            $query->orWhere('element_1_id', $elementId)
                ->orWhere('element_2_id', $elementId)
                ->orWhere('element_3_id', $elementId)
                ->orWhere('element_4_id', $elementId);
        }
    })->get()->each(function (Dragon $dragon) use ($elementIdMap, &$fixed) {
        $changed = false;

        foreach (['element_1_id', 'element_2_id', 'element_3_id', 'element_4_id'] as $field) {
            $value = $dragon->{$field};
            if ($value !== null && isset($elementIdMap[$value])) {
                $rarity = $elementIdMap[$value];
                if ($dragon->rarity_id !== $rarity->id) {
                    $dragon->rarity_id = $rarity->id;
                }
                $dragon->{$field} = null;
                $changed = true;
            }
        }

        if ($changed) {
            $dragon->save();
            $fixed++;
        }
    });

    $this->info("Repaired {$fixed} dragon(s) by moving Mythical/Heroic from elements to rarity.");
})->purpose('Repair dragons that incorrectly stored Mythical/Heroic as element instead of rarity');
