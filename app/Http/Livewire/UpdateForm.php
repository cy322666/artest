<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\Show;
use App\Models\Staff;
use App\Services\amoCRM\Client;
use App\Services\Telegram;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Livewire\Component;

class UpdateForm extends Component
{
    public string $title = 'Изменение показа';

    public Show $show;

    public string $object;
    public string $type;
    public string $phone;
    public string $name;
    public string $datetime;
    public int $responsible_user_id;
    public string $city;

    public array $staffs;
    public string $staff;

    public bool $isClose = false;

    /**
     * @throws Exception
     */
    public function render(Request $request): Factory|View|Application
    {
        $this->show = $request->show ?? $this->show;
        $this->staffs = Staff::query()->get()->toArray();

        if ($request->city)
            $this->city = $request->city;

        if ($request->show) {

            $this->object = $this->show->object;
            $this->type   = $this->show->type;
            $this->status = $this->show->status;
            $this->datetime = $this->show->datetime;
            $this->name = $this->show->name;
            $this->isClose = $this->show->is_close;
            $this->staff = $this->show->responsible_user_id;
        }

        return view('livewire.update-form');
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function save()
    {
        $this->show->update([
            'status'  => $this->status,
            'object'  => $this->object,
            'type'    => $this->type,
            'name'    => $this->name,
            'is_close' => $this->status == (2 || 3),
            'responsible_user_id' => $this->staff,
            'datetime' => Carbon::parse($this->datetime)->format('Y-m-d H:i'),
        ]);

        Telegram::pushChat($this->show);

        $amoApi = (new Client(Account::query()->first()))->init();

        $lead = $amoApi->service->leads()->find($this->show->lead_id);

        if ($this->show->status == 1) {

            $lead->status_id = $this->show->matchCameStatusId();
            $lead->save();
        }

        $note = $lead->createNote();
        $note->text = $this->show->buildTextAmo();
        $note->save();

        redirect('https://'.env('AMOCRM_SUBDOMAIN').'.amocrm.ru/leads/detail/'.$lead->id);
    }
}
