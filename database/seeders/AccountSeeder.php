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
        $accounts = array (
  0 => 
  array (
    'id' => 1,
    'account_name' => 'Mustang',
    'fb_mail' => 'kenjirojunior@gmail.com',
    'gmail' => 'kenjirojunior@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  1 => 
  array (
    'id' => 3,
    'account_name' => 'Mugezh',
    'fb_mail' => '',
    'gmail' => 'achyasir27rofiqi@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  2 => 
  array (
    'id' => 5,
    'account_name' => 'mustang 67',
    'fb_mail' => '',
    'gmail' => 'yasir27berbisnis@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  3 => 
  array (
    'id' => 6,
    'account_name' => 'mustang selby',
    'fb_mail' => '',
    'gmail' => 'crystalmaiden449@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  4 => 
  array (
    'id' => 7,
    'account_name' => 'm arafat',
    'fb_mail' => '',
    'gmail' => 'arafatmustang@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  5 => 
  array (
    'id' => 9,
    'account_name' => 'mustang spectre',
    'fb_mail' => 'spectredragon32@gmail.com',
    'gmail' => 'spectredragon32@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  6 => 
  array (
    'id' => 10,
    'account_name' => 'mustang assasin',
    'fb_mail' => 'dragon11mustang@gmail.com',
    'gmail' => 'dragon11mustang@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  7 => 
  array (
    'id' => 11,
    'account_name' => 'mustang 12',
    'fb_mail' => 'dragon12mustang@gmail.com',
    'gmail' => 'dragon12mustang@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  8 => 
  array (
    'id' => 12,
    'account_name' => 'mustang 13',
    'fb_mail' => 'dragon13mustang@gmail.com',
    'gmail' => 'dragon13mustang@gmail.com',
    'ms_mail' => '-',
    'account_status' => 'active',
  ),
  9 => 
  array (
    'id' => 14,
    'account_name' => 'laravue',
    'fb_mail' => '-',
    'gmail' => '-',
    'ms_mail' => 'laravue27@outlook.com',
    'account_status' => 'active',
  ),
  10 => 
  array (
    'id' => 15,
    'account_name' => 'django',
    'fb_mail' => '-',
    'gmail' => '-',
    'ms_mail' => 'django27py@outlook.com',
    'account_status' => 'active',
  ),
  11 => 
  array (
    'id' => 16,
    'account_name' => 'mongodb',
    'fb_mail' => '-',
    'gmail' => '-',
    'ms_mail' => 'mongodb27@outlook.com',
    'account_status' => 'active',
  ),
  12 => 
  array (
    'id' => 4,
    'account_name' => 'mustang gt',
    'fb_mail' => NULL,
    'gmail' => 'rofiqidc@gmail.com',
    'ms_mail' => '-@mail.com',
    'account_status' => 'active',
  ),
  13 => 
  array (
    'id' => 2,
    'account_name' => 'maduradev',
    'fb_mail' => NULL,
    'gmail' => 'rofiqiachyasir@gmail.com',
    'ms_mail' => '-@mail.com',
    'account_status' => 'active',
  ),
  14 => 
  array (
    'id' => 13,
    'account_name' => 'Mustang Convertible',
    'fb_mail' => NULL,
    'gmail' => 'mustang97muscle@gmail.com',
    'ms_mail' => '-@mail.com',
    'account_status' => 'active',
  ),
  15 => 
  array (
    'id' => 8,
    'account_name' => 'Mustang Sniper',
    'fb_mail' => 'sniperdragon732@gmail.com',
    'gmail' => 'sniperdragon732@gmail.com',
    'ms_mail' => '-@mail.com',
    'account_status' => 'active',
  ),
);

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['id' => $account['id']],
                $account
            );
        }
    }
}