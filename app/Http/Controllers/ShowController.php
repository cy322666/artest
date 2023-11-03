<?php

namespace App\Http\Controllers;

use App\Http\Middleware\ApiException;
use App\Http\Resources\ShowCollection;
use App\Http\Resources\ShowDetailResource;
use App\Models\Account;
use App\Models\Show;
use App\Services\amoCRM\Client;
use App\Services\Telegram;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class ShowController extends Controller
{
    /**
     * Создается показ в амо
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function create(string $leadId, Request $request)
    {
        $show = Show::query()->create([
            'lead_id' => $leadId,
            'status'  => $request->status,
            'object'  => $request->object,
            'type'    => $request->type,
            'name'    => $request->name,
            'is_new'  => false, //новый контакт
            'is_close' => false,
            'datetime' => Carbon::parse($request->datetime)->format('Y-m-d H:i').':00',
            'pipeline_id' => (int)$request->pipeline_id,
        ]);

        Telegram::pushChat($show);

        $amoApi = (new Client(Account::query()->first()))->init();

        $lead = $amoApi->service->leads()->find($show->lead_id);

        $note = $lead->createNote();
        $note->text = $show->buildTextAmo();
        $note->save();

        return new ShowDetailResource($show);
    }

    /**
     * изменение показа
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function update(int $leadId, Show $show, Request $request)
    {
        $show->update([
            'status'  => $request->status,
            'object'  => $request->object,
            'type'    => $request->type,
            'name'    => $request->name,
            'is_close' => $request->status == (1 || -1),
            'datetime' => Carbon::parse($request->datetime)->format('Y-m-d H:i').':00',
        ]);

        Telegram::pushChat($show);

        $amoApi = (new Client(Account::query()->first()))->init();

        $lead = $amoApi->service->leads()->find($show->lead_id);

        $note = $lead->createNote();
        $note->text = $show->buildTextAmo();
        $note->save();

        return new ShowDetailResource($show);
    }

    //все показы по сделке
    public function list(string $leadId)
    {
        return new ShowCollection(Show::whereLeadId($leadId)->get()->sortBy('datetime', 'ASC'));
    }
}
