<?php

namespace App\Http\Livewire;

use App\Models\Account;
use App\Models\Show;
use App\Services\amoCRM\Client;
use App\Services\amoCRM\Models\Contacts;
use App\Services\amoCRM\Models\Leads;
use App\Services\Telegram;
use Carbon\Carbon;
use Exception;
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
    public string $datetime;

    public bool $isNew = false;

    public function render(): Factory|View|Application
    {
        return view('livewire.create-form');
    }

    /**
     * @throws Exception
     */
    public function save(Request $request)
    {
        Log::info(__METHOD__, $request->toArray());

        $amoApi = (new Client(Account::query()->first()))->init();

        $contact = Contacts::search([
            'Телефоны' => [$this->phone]
        ], $amoApi);

        if (!$contact) {

            $this->isNew = true;

            $contact = Contacts::create($amoApi, 'Новый клиент');
            $contact = Contacts::update($contact, ['Телефоны' => [$this->phone]]);
        }

        $lead = Leads::search($contact, $amoApi);

        $lead = $lead !== false ? $lead : Leads::create($contact, [
            'pipeline_id' => $request->pipeline_id,
        ], 'Новый показ');

        $show = Show::query()->create([
            'lead_id' => $lead->id,
            'status'  => 0,
            'object'  => $this->object,
            'type'    => $this->type,
            'name'    => $contact->name,
            'is_new'  => $this->isNew,
            'is_close' => false,
            'datetime' => Carbon::parse($this->datetime)->format('Y-m-d H:i'),
            'pipeline_id' => $lead->pipeline_id,
        ]);

        Telegram::pushChat($show);

        $note = $lead->createNote();
        $note->text = $show->buildTextAmo();
        $note->save();

        //TODO redirect to chat?
    }
}
