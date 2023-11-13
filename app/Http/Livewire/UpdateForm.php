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

class UpdateForm extends Component
{
    public string $title = 'Изменение показа';

    public Show $show;

    public $id;

    public string $object;
    public string $status;
    public string $type;
    public string $datetime;

    public bool $isNew = false;
    public bool $isClose = false;

    /**
     * @throws Exception
     */
    public function render(Request $request): Factory|View|Application
    {
        $this->show = $request->show ?? $this->show;

        if ($request->show) {

            $this->object = $this->show->object;
            $this->type   = $this->show->type;
            $this->status = $this->show->status;
            $this->datetime = $this->show->datetime;
            $this->isNew = $this->show->is_new;
            $this->isClose = $this->show->is_close;
            $this->id = $this->show->id;
        }

        return view('livewire.update-form');
    }

    /**
     * @throws Exception
     */
    public function save()
    {
        $this->show->update([
            'status'  => $this->status,
            'object'  => $this->object,
            'type'    => $this->type,
            'is_close' => $this->status == (2 || 3),
            'datetime' => Carbon::parse($this->datetime)->format('Y-m-d H:i'),
        ]);

        Telegram::pushChat($this->show);

        $amoApi = (new Client(Account::query()->first()))->init();

        $lead = $amoApi->service->leads()->find($this->show->lead_id);

        $note = $lead->createNote();
        $note->text = $this->show->buildTextAmo();
        $note->save();

        //TODO redirect to chat?
    }
}
