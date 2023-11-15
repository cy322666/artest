<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\Staff;
use App\Services\amoCRM\Client;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class GetStaffs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'amocrm:staffs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Exception
     */
    public function handle()
    {
        $amoApi = (new Client(account: Account::query()->first()))->init();

        $staffs = $amoApi->service->account->users;

        Staff::query()->truncate();

        foreach ($staffs as $staff) {

            Staff::query()->create([
                'staff_id' => $staff->id,
                'name'     => $staff->name,
            ]);
        }

        return CommandAlias::SUCCESS;
    }
}
