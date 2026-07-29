<?php

namespace App\Http\Controllers;

use App\Models\Element;
use Illuminate\Http\Request;

class ElementController extends Controller
{
    public function index()
    {
        $elements = Element::latest()->get();

        return view('elements.index', compact('elements'));
    }

    public function create()
    {
        return view('elements.create');
    }

    public function updateSeeder()
    {
        $defaultElements = [
            ['id' => 1, 'name' => 'Terra', 'alias' => 'terra'],
            ['id' => 2, 'name' => 'Flame', 'alias' => 'flame'],
            ['id' => 3, 'name' => 'Sea', 'alias' => 'sea'],
            ['id' => 4, 'name' => 'Nature', 'alias' => 'nature'],
            ['id' => 5, 'name' => 'Electric', 'alias' => 'electric'],
            ['id' => 6, 'name' => 'Ice', 'alias' => 'ice'],
            ['id' => 7, 'name' => 'Metal', 'alias' => 'metal'],
            ['id' => 8, 'name' => 'Dark', 'alias' => 'dark'],
            ['id' => 9, 'name' => 'Light', 'alias' => 'light'],
            ['id' => 10, 'name' => 'War', 'alias' => 'war'],
            ['id' => 11, 'name' => 'Pure', 'alias' => 'pure'],
            ['id' => 12, 'name' => 'Legend', 'alias' => 'legend'],
            ['id' => 13, 'name' => 'Beauty', 'alias' => 'beauty'],
            ['id' => 14, 'name' => 'Ancient', 'alias' => 'ancient'],
            ['id' => 15, 'name' => 'Chaos', 'alias' => 'chaos'],
            ['id' => 16, 'name' => 'Magic', 'alias' => 'magic'],
            ['id' => 17, 'name' => 'Dream', 'alias' => 'dream'],
            ['id' => 18, 'name' => 'Soul', 'alias' => 'soul'],
            ['id' => 19, 'name' => 'Happy', 'alias' => 'happy'],
            ['id' => 20, 'name' => 'Primal', 'alias' => 'primal'],
            ['id' => 21, 'name' => 'Wind', 'alias' => 'wind'],
            ['id' => 22, 'name' => 'Time', 'alias' => 'time'],
        ];

        $elements = Element::query()
            ->select(['id', 'name', 'alias'])
            ->get();

        $payload = $elements->isNotEmpty()
            ? $elements->map(function ($element) {
                return [
                    'id' => $element->id,
                    'name' => $element->name,
                    'alias' => $element->alias,
                ];
            })->values()->all()
            : $defaultElements;

        $seederPath = base_path('database/seeders/ElementSeeder.php');
        $template = <<<'PHP'
<?php

namespace Database\Seeders;

use App\Models\Element;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $elements = %s;

        foreach ($elements as $element) {
            Element::updateOrCreate(
                ['id' => $element['id']],
                $element
            );
        }
    }
}
PHP;

        file_put_contents($seederPath, sprintf($template, var_export($payload, true)));

        return redirect()->route('elements.index')->with('success', 'ElementSeeder berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
        ]);

        Element::create($request->only(['name', 'alias']));

        return redirect()->route('elements.index')->with('success', 'Element created successfully.');
    }

    public function edit(Element $element)
    {
        return view('elements.edit', compact('element'));
    }

    public function update(Request $request, Element $element)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'alias' => 'nullable|string|max:255',
        ]);

        $element->update($request->only(['name', 'alias']));

        return redirect()->route('elements.index')->with('success', 'Element updated successfully.');
    }

    public function destroy(Element $element)
    {
        $element->delete();

        return redirect()->route('elements.index')->with('success', 'Element deleted successfully.');
    }
}
