<?php

namespace App\Http\Controllers;

use App\Models\Dragon;
use App\Models\Element;
use App\Models\Rarity;
use App\Services\DragonBookScraper;
use Illuminate\Http\Request;
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
            ->orderBy('dragon_book', $direction)
            ->paginate(12)
            ->withQueryString();

        return view('dragons.index', compact('dragons', 'rarities'));
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

    public function truncate()
    {
        $this->resetDragonTable();

        return redirect()->route('dragons.index')->with('success', 'Dragon data truncated successfully.');
    }

    private function resetDragonTable(): void
    {
        $connection = Dragon::query()->getConnection();

        $connection->statement('TRUNCATE TABLE dragon_owning_details, dragon_ownings, dragons RESTART IDENTITY CASCADE');
    }
}
