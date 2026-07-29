<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');

        $allowedSortFields = ['account_name', 'fb_mail', 'gmail', 'ms_mail', 'account_status', 'created_at'];
        if (!in_array($sortField, $allowedSortFields, true)) {
            $sortField = 'created_at';
        }

        $direction = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        $accounts = Account::query()
            ->orderBy($sortField, $direction)
            ->get();

        return view('accounts.index', compact('accounts', 'sortField', 'direction'));
    }

    public function create()
    {
        return view('accounts.create');
    }

    public function updateSeeder()
    {
        $accounts = Account::query()
            ->select(['id', 'account_name', 'fb_mail', 'gmail', 'ms_mail', 'account_status'])
            ->get();

        $payload = $accounts->map(function ($account) {
            return [
                'id' => $account->id,
                'account_name' => $account->account_name,
                'fb_mail' => $account->fb_mail,
                'gmail' => $account->gmail,
                'ms_mail' => $account->ms_mail,
                'account_status' => $account->account_status,
            ];
        })->values()->all();

        $seederPath = base_path('database/seeders/AccountSeeder.php');
        $template = <<<'PHP'
<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = %s;

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['id' => $account['id']],
                $account
            );
        }
    }
}
PHP;

        file_put_contents($seederPath, sprintf($template, var_export($payload, true)));

        return redirect()->route('accounts.index')->with('success', 'AccountSeeder berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'fb_mail' => 'nullable|email',
            'gmail' => 'nullable|email',
            'ms_mail' => 'nullable|email',
            'account_status' => 'required|string|max:50',
        ]);

        Account::create($request->all());

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        return view('accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'fb_mail' => 'nullable|email',
            'gmail' => 'nullable|email',
            'ms_mail' => 'nullable|email',
            'account_status' => 'required|string|max:50',
        ]);

        $account->update($request->all());

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
