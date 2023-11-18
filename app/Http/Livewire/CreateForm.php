<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\Show;
use App\Models\Staff;
use App\Services\amoCRM\Client;
use App\Services\amoCRM\Models\Contacts;
use App\Services\amoCRM\Models\Leads;
use App\Services\Telegram;
use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CreateForm extends Component
{
    public string $title = 'Создание показа';

    public string $object;
    public string $type;
    public string $phone;
    public string $name;
    public string $datetime;
    public int $responsible_user_id;
    public string $city;

    public array $staffs;
    public string $staff;

    public bool $isNew = false;

    public function render(Request $request): Factory|View|Application
    {
        Log::info(__METHOD__, $request->toArray());

        $this->staffs = Staff::query()->get()->toArray();

        if ($request->city)
            $this->city = $request->city;

        return view('livewire.create-form');
    }

    /**
     * @throws GuzzleException
     */
    public function save(Request $request)
    {
        Log::info(__METHOD__, $request->toArray());

        $show = Show::query()->create([
            'status'  => 0,
            'object'  => $this->object,
            'type'    => $this->type,
            'name'    => $this->name,
            'is_new'  => $this->isNew,
            'is_close' => false,
            'pipeline_id' => Show::matchPipelineIdByCity($this->city),
            'responsible_user_id' => $this->staff,
            'datetime' => Carbon::parse($this->datetime)->format('Y-m-d H:i'),
        ]);

        $amoApi = (new Client(Account::query()->first()))->init();

        $contact = Contacts::search(['Телефоны' => [$this->phone]], $amoApi);

        if (!$contact) {

            $this->isNew = true;

            $contact = Contacts::create($amoApi, $show->name);
            $contact = Contacts::update($contact, ['Телефоны' => [$this->phone]]);
        } else
            $lead = Leads::search($contact, $amoApi);

        $lead = !empty($lead) ? $lead : Leads::create($contact, [
            'pipeline_id' => $show->pipeline_id,
            'responsible_user_id' => $show->staff,
        ], 'Новый показ');

        $show->lead_id = $lead->id;
        $show->save();

        if ($show->status == 1) {

            $lead->status_id = $show->matchCameStatusId();
            $lead->save();
        }

        Telegram::pushChat($show);

        $note = $lead->createNote();
        $note->text = $show->buildTextAmo();
        $note->save();

        redirect('https://'.env('AMOCRM_SUBDOMAIN').'.amocrm.ru/leads/detail/'.$lead->id);
    }
}
