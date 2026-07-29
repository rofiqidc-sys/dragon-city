<?php

namespace App\Http\Controllers;

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use App\Services\DragonBookScraper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DragonController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->input('sort', 'asc');
        $direction = strtolower($sort) === 'desc' ? 'desc' : 'asc';
        $rarities = Rarity::orderBy('name')->get();

        $dragons = Dragon::with('rarity', 'element1', 'element2', 'element3', 'element4')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = Str::lower(trim($request->input('search')));

                $query->where(function ($query) use ($search) {
                    $pattern = "%{$search}%";

                    $query->whereRaw('LOWER(dragon_name) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(dragon_book) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(alias) LIKE ?', [$pattern])
                        ->orWhereHas('rarity', fn ($rarityQuery) => $rarityQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]))
                        ->orWhereHas('element1', fn ($elementQuery) => $elementQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]))
                        ->orWhereHas('element2', fn ($elementQuery) => $elementQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]))
                        ->orWhereHas('element3', fn ($elementQuery) => $elementQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]))
                        ->orWhereHas('element4', fn ($elementQuery) => $elementQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]));
                });
            })
            ->when($request->filled('rarity'), function ($query) use ($request) {
                $query->whereHas('rarity', fn ($rarityQuery) => $rarityQuery->where('id', $request->input('rarity')));
            })
            ->orderByRaw("CASE WHEN dragon_book ~ '^[0-9]+$' THEN 1 ELSE 0 END DESC")
            ->orderByRaw("LPAD(COALESCE(NULLIF(dragon_book, ''), '0'), 10, '0') " . $direction)
            ->orderBy('dragon_name')
            ->paginate(12)
            ->withQueryString();

        $bestHeroicDragons = Dragon::with('rarity')
            ->where('is_best_heroic', true)
            ->latest('updated_at')
            ->get();

        return view('dragons.index', compact('dragons', 'rarities', 'bestHeroicDragons'));
    }

    public function masterDragon(Request $request)
    {
        $accountId = 1;
        $search = trim((string) $request->query('search', ''));
        $selectedRarity = $request->query('rarity');
        $rarities = Rarity::orderBy('name')->get();

        $dragons = Dragon::with('rarity', 'element1', 'element2', 'element3', 'element4')
            ->leftJoin('orb_ownings', function ($join) use ($accountId) {
                $join->on('dragons.id', '=', 'orb_ownings.dragon_id')
                    ->where('orb_ownings.account_id', '=', $accountId);
            })
            ->whereDoesntHave('dragonOwningDetails', function ($query) use ($accountId) {
                $query->where('account_id', $accountId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $pattern = '%' . Str::lower($search) . '%';
                    $query->whereRaw('LOWER(dragon_name) LIKE ?', [$pattern])
                        ->orWhereRaw('LOWER(dragon_book) LIKE ?', [$pattern])
                        ->orWhereHas('rarity', fn ($rarityQuery) => $rarityQuery->whereRaw('LOWER(name) LIKE ?', [$pattern]));
                });
            })
            ->when($selectedRarity, function ($query) use ($selectedRarity) {
                $query->where('rarity_id', $selectedRarity);
            })
            ->select('dragons.*', DB::raw('COALESCE(orb_ownings.jumlah_orb, 0) as jumlah_orb'))
            ->orderByRaw("CASE WHEN dragon_book ~ '^[0-9]+$' THEN 1 ELSE 0 END DESC")
            ->orderByRaw("LPAD(COALESCE(NULLIF(dragon_book, ''), '0'), 10, '0')")
            ->orderBy('dragon_name')
            ->paginate(16)
            ->withQueryString();

        return view('master-dragons.index', compact('dragons', 'rarities', 'search', 'selectedRarity'));
    }

    public function create()
    {
        $rarities = Rarity::all();
        $elements = Element::all();

        return view('dragons.create', compact('rarities', 'elements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dragon_name' => 'required|string|max:255',
            'rarity_id' => 'nullable|exists:rarities,id',
            'element_1_id' => 'required|exists:elements,id',
            'element_2_id' => 'nullable|exists:elements,id',
            'element_3_id' => 'nullable|exists:elements,id',
            'element_4_id' => 'nullable|exists:elements,id',
            'summon_time' => 'nullable|integer',
            'orb_to_summon' => 'required|integer|min:0',
            'hatching_time' => 'required|integer|min:0',
        ]);

        Dragon::create($request->all());

        return redirect()->route('dragons.index')->with('success', 'Dragon created successfully.');
    }

    public function scrape(Request $request, DragonBookScraper $scraper)
    {
        $parameter = $request->input('parameter', '0001-0100');
        $dragons = $scraper->fetch($parameter);

        if ($dragons === []) {
            return response()->json([
                'message' => 'Tidak ada data yang berhasil diambil.',
                'parameter' => $parameter,
                'debug' => [
                    'scraper_class' => get_class($scraper),
                    'parameter_normalized' => $scraper->fetch('0001-0100') === [] ? 'empty' : 'has-data',
                ],
            ], 502);
        }

        foreach ($dragons as $dragonData) {
            $rarity = $this->resolveRarity($dragonData['rarity'] ?? null);
            $elements = $this->resolveElements($dragonData['element'] ?? []);

            $dragon = Dragon::firstOrNew([
                'dragon_book' => $dragonData['number'] ?? null,
            ]);

            $dragon->fill([
                'dragon_name' => $dragonData['name'] ?? null,
                'rarity_id' => $rarity?->id,
                'element_1_id' => $elements[0] ?? null,
                'element_2_id' => $elements[1] ?? null,
                'element_3_id' => $elements[2] ?? null,
                'element_4_id' => $elements[3] ?? null,
                'orb_to_summon' => 0,
                'summon_time' => null,
                'hatching_time' => 0,
            ]);

            $dragon->save();
        }

        return response()->json([
            'parameter' => $parameter,
            'count' => count($dragons),
            'saved' => true,
            'data' => $dragons,
        ]);
    }

    public function generateAliases()
    {
        $generated = [];

        foreach (Dragon::cursor() as $dragon) {
            $name = Str::upper(Str::ascii((string) $dragon->dragon_name));
            $name = preg_replace('/[^A-Z0-9]/', '', $name) ?: 'DRAGON';
            $baseAlias = str_pad(substr($name, 0, 7), 7, 'X');
            $alias = $baseAlias;
            $counter = 0;

            while (
                isset($generated[$alias])
                || Dragon::where('alias', $alias)
                    ->where($dragon->getQualifiedKeyName(), '!=', $dragon->getKey())
                    ->exists()
            ) {
                $counter++;
                $suffix = str_pad(strtoupper(base_convert($counter, 10, 36)), 3, '0', STR_PAD_LEFT);
                $alias = substr($baseAlias, 0, 4) . substr($suffix, -3);
            }

            $generated[$alias] = true;
            $dragon->update(['alias' => $alias]);
        }

        return redirect()->route('dragons.index')->with('success', count($generated) . ' dragon alias berhasil dibuat.');
    }

    public function exportSeederArray()
    {
        $dragons = Dragon::query()
            ->select([
                'dragon_book',
                'alias',
                'dragon_name',
                'rarity_id',
                'element_1_id',
                'element_2_id',
                'element_3_id',
                'element_4_id',
                'summon_time',
                'orb_to_summon',
                'hatching_time',
                'is_best_heroic',
            ])
            ->get();

        $payload = $dragons->map(function ($dragon) {
            return [
                'dragon_book' => $dragon->dragon_book,
                'alias' => $dragon->alias,
                'dragon_name' => $dragon->dragon_name,
                'rarity_id' => $dragon->rarity_id,
                'element_1_id' => $dragon->element_1_id,
                'element_2_id' => $dragon->element_2_id,
                'element_3_id' => $dragon->element_3_id,
                'element_4_id' => $dragon->element_4_id,
                'summon_time' => $dragon->summon_time,
                'orb_to_summon' => $dragon->orb_to_summon,
                'hatching_time' => $dragon->hatching_time,
                'is_best_heroic' => (bool) $dragon->is_best_heroic,
            ];
        })->values()->all();

        $seederPath = base_path('database/seeders/DragonSeeder.php');
        $template = <<<'PHP'
<?php

namespace Database\Seeders;

use App\Models\Dragon;
use Illuminate\Database\Seeder;

class DragonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dragons = %s;

        foreach ($dragons as $dragon) {
            Dragon::updateOrCreate(
                ['dragon_book' => $dragon['dragon_book']],
                $dragon
            );
        }
    }
}
PHP;

        $content = sprintf($template, var_export($payload, true));
        file_put_contents($seederPath, $content);

        return redirect()->route('dragons.index')->with('success', 'DragonSeeder berhasil diperbarui.');
    }

    private function resolveRarity(?string $name): ?Rarity
    {
        if ($name === null || trim($name) === '') {
            return null;
        }

        $normalized = trim($name);
        $existingRarity = Rarity::whereRaw('LOWER(name) = ?', [Str::lower($normalized)])->first();

        if ($existingRarity) {
            return $existingRarity;
        }

        return Rarity::create([
            'name' => $normalized,
            'alias' => Str::slug($normalized),
            'key_need_to_summon' => null,
        ]);
    }

    private function resolveElements(array $elements): array
    {
        $resolved = [];

        foreach ($elements as $element) {
            if (!is_string($element) || trim($element) === '') {
                continue;
            }

            $normalized = trim($element);
            $model = Element::firstOrCreate(
                ['name' => $normalized],
                ['alias' => Str::slug($normalized)]
            );

            $resolved[] = $model->id;
        }

        return array_slice($resolved, 0, 4);
    }

    public function edit(Dragon $dragon)
    {
        $rarities = Rarity::all();
        $elements = Element::all();

        return view('dragons.edit', compact('dragon', 'rarities', 'elements'));
    }

    public function update(Request $request, Dragon $dragon)
    {
        $request->validate([
            'dragon_name' => 'required|string|max:255',
            'rarity_id' => 'nullable|exists:rarities,id',
            'element_1_id' => 'required|exists:elements,id',
            'element_2_id' => 'nullable|exists:elements,id',
            'element_3_id' => 'nullable|exists:elements,id',
            'element_4_id' => 'nullable|exists:elements,id',
            'summon_time' => 'nullable|integer',
            'orb_to_summon' => 'required|integer|min:0',
            'hatching_time' => 'required|integer|min:0',
        ]);

        $dragon->update($request->all());

        return redirect()->route('dragons.index')->with('success', 'Dragon updated successfully.');
    }

    public function destroy(Dragon $dragon)
    {
        $dragon->delete();

        return redirect()->route('dragons.index')->with('success', 'Dragon deleted successfully.');
    }

    public function markBestHeroic(Dragon $dragon)
    {
        if (! $dragon->is_best_heroic) {
            $dragon->update(['is_best_heroic' => true]);
        }

        return redirect()->route('dragons.index')->with('success', 'Dragon marked as Best Heroic.');
    }

    public function truncate(Request $request)
    {
        $request->validate([
            'confirmation' => ['required', 'in:TRUNCATE DRAGONS'],
        ]);

        $backupPath = $this->createDragonBackup();
        $this->resetDragonTable();

        return redirect()->route('dragons.index')->with('success', 'Dragon data truncated successfully. Backup created: ' . $backupPath);
    }

    public function restoreLatest(Request $request)
    {
        $request->validate([
            'confirmation' => ['required', 'in:RESTORE DRAGONS'],
        ]);

        $backupPath = collect(Storage::disk('local')->files('dragon-backups'))
            ->sortDesc()
            ->first();

        if ($backupPath === null) {
            return redirect()->route('dragons.index')->with('error', 'No dragon backup is available.');
        }

        $backup = json_decode(Storage::disk('local')->get($backupPath), true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($backup) {
            $this->resetDragonTable();

            foreach (['dragons', 'dragon_ownings', 'dragon_owning_details'] as $table) {
                if (! empty($backup[$table])) {
                    DB::table($table)->insert($backup[$table]);
                }
            }
        });

        return redirect()->route('dragons.index')->with('success', 'Latest dragon backup restored successfully.');
    }

    private function resetDragonTable(): void
    {
        $connection = Dragon::query()->getConnection();

        $connection->statement('TRUNCATE TABLE dragon_owning_details, dragon_ownings, dragons RESTART IDENTITY CASCADE');
    }

    private function createDragonBackup(): string
    {
        $backup = [
            'created_at' => now()->toIso8601String(),
            'dragons' => DB::table('dragons')->get()->map(fn ($row) => (array) $row)->all(),
            'dragon_ownings' => DB::table('dragon_ownings')->get()->map(fn ($row) => (array) $row)->all(),
            'dragon_owning_details' => DB::table('dragon_owning_details')->get()->map(fn ($row) => (array) $row)->all(),
        ];

        $path = 'dragon-backups/' . now()->format('Ymd_His_u') . '.json';
        Storage::disk('local')->put($path, json_encode($backup, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $path;
    }
}
