<?php

namespace App\Http\Controllers;

use App\Models\Rarity;
use Illuminate\Http\Request;

class RarityController extends Controller
{
    public function index()
    {
        $rarities = Rarity::latest()->get();

        return view('rarities.index', compact('rarities'));
    }

    public function create()
    {
        return view('rarities.create');
    }

    public function updateSeeder()
    {
        $defaultRarities = [
            ['id' => 1, 'name' => 'Common', 'alias' => 'c', 'key_need_to_summon' => null],
            ['id' => 2, 'name' => 'Rare', 'alias' => 'r', 'key_need_to_summon' => null],
            ['id' => 3, 'name' => 'Very Rare', 'alias' => 'vr', 'key_need_to_summon' => null],
            ['id' => 4, 'name' => 'Epic', 'alias' => 'e', 'key_need_to_summon' => null],
            ['id' => 5, 'name' => 'Legendary', 'alias' => 'l', 'key_need_to_summon' => null],
            ['id' => 6, 'name' => 'Mythical', 'alias' => 'm', 'key_need_to_summon' => null],
            ['id' => 7, 'name' => 'Heroic', 'alias' => 'h', 'key_need_to_summon' => null],
            ['id' => 8, 'name' => 'Divine', 'alias' => 'd', 'key_need_to_summon' => null],
            ['id' => 9, 'name' => 'Pure', 'alias' => 'p', 'key_need_to_summon' => null],
            ['id' => 10, 'name' => 'Ancient', 'alias' => 'an', 'key_need_to_summon' => null],
            ['id' => 11, 'name' => 'Mystic', 'alias' => 'me', 'key_need_to_summon' => null],
        ];

        $rarities = Rarity::query()
            ->select(['id', 'name', 'alias', 'key_need_to_summon'])
            ->get();

        $payload = $rarities->isNotEmpty() ? $rarities->map(function ($rarity) {
            return [
                'id' => $rarity->id,
                'name' => $rarity->name,
                'alias' => $rarity->alias,
                'key_need_to_summon' => $rarity->key_need_to_summon,
            ];
        })->values()->all() : $defaultRarities;

        $seederPath = base_path('database/seeders/RaritySeeder.php');
        $template = <<<'PHP'
<?php

namespace Database\Seeders;

use App\Models\Rarity;
use Illuminate\Database\Seeder;

class RaritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rarities = %s;

        foreach ($rarities as $rarity) {
            Rarity::updateOrCreate(
                ['id' => $rarity['id']],
                $rarity
            );
        }
    }
}
PHP;

        file_put_contents($seederPath, sprintf($template, var_export($payload, true)));

        return redirect()->route('rarities.index')->with('success', 'RaritySeeder berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'key_need_to_summon' => 'nullable|string|max:255',
        ]);

        Rarity::create($request->all());

        return redirect()->route('rarities.index')->with('success', 'Rarity created successfully.');
    }

    public function edit(Rarity $rarity)
    {
        return view('rarities.edit', compact('rarity'));
    }

    public function update(Request $request, Rarity $rarity)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
            'key_need_to_summon' => 'nullable|string|max:255',
        ]);

        $rarity->update($request->all());

        return redirect()->route('rarities.index')->with('success', 'Rarity updated successfully.');
    }

    public function destroy(Rarity $rarity)
    {
        $rarity->delete();

        return redirect()->route('rarities.index')->with('success', 'Rarity deleted successfully.');
    }
}
