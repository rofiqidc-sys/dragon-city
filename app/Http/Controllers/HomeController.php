<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Dragon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::with('orbOwnings.dragon')
            ->orderBy('account_name')
            ->get();

        $bestHighDragons = Dragon::where('is_best_heroic', true)
            ->orderBy('dragon_name')
            ->get(['id', 'alias', 'dragon_name']);

        return view('home', compact('accounts', 'bestHighDragons'));
    }

    public function runSeeder(Request $request)
    {
        $request->validate([
            'seed' => 'required|string',
        ]);

        $seed = trim($request->input('seed'));

        $seeders = [];

        if ($seed === 'all') {
            $seeders = ['AccountSeeder', 'ElementSeeder', 'RaritySeeder', 'BestHeroicSeeder'];
        } else {
            $class = $seed;
            if (!str_ends_with($class, 'Seeder')) {
                $class = $class . 'Seeder';
            }

            $seeders = [$class];
        }

        foreach ($seeders as $class) {
            $fullClass = 'Database\\Seeders\\' . $class;

            if (!class_exists($fullClass)) {
                return redirect()->route('home')->with('seed_status', 'Seeder ' . $class . ' tidak ditemukan.');
            }

            Artisan::call('db:seed', ['--class' => $class]);
        }

        $message = $seed === 'all'
            ? 'Semua seeder dijalankan.'
            : 'Seeder ' . $seeders[0] . ' dijalankan.';

        return redirect()->route('home')->with('seed_status', $message);
    }
}
