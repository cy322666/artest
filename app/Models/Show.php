<?php

namespace App\Models;

use App\Services\amoCRM\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Show extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'status',
        'object',
        'type',
        'name',
        'is_new',
        'is_close',
        'datetime',
        'pipeline_id',
        'responsible_user_id',
    ];

    public function matchStatus() : string
    {
        return match ((int)$this->status) {

            0 => 'запланирована',
            1 => 'проведена (изменено)',
            2 => 'отменена (изменено)',
            3 => 'перенесена (изменено)',
        };
    }

    public function matchChatId(string $city) : string
    {
        return match ($city) {

            'msk'   => -4075342320,//'-1001298056437',
            'spb'   => -4075342320,//'-1001275226849',
            'dubai' => -4075342320,//'-1002085907391',
        };
    }

    public function matchChatIdByPipeline() : string
    {
        return match ($this->pipeline_id) {

            1287231 => -4075342320,//'-1001298056437',
            400942  => -4075342320,//'-1001275226849',
            5219508 => -4075342320,//'-1002085907391',
        };
    }

    public static function matchPipelineIdByCity(string $city): int
    {
        return match ($city) {

            'msk'  => 1287231,
            'spb'  => 400942,
            'dubai'=> 5219508,
        };
    }

    public function matchCameStatusId(): int
    {
        return match ($this->pipeline_id) {

            1287231 => 21145983,
            400942  => 16970728,
            5219508 => 46654344,
        };
    }

    public function matchCityByPipelineId(): string
    {
        return match ($this->pipeline_id) {

            1287231 => 'msk',
            400942  => 'spb',
            5219508 => 'dubai',
        };
    }

    public function buildTitleTg() : string
    {
        return '*Встреча '.$this->matchStatus()."*\n";
    }

    public function getStaffName($leadId) : string
    {
        $amoApi = (new Client(Account::query()->first()))->init();

        $lead = $amoApi->service->leads()->find($this->lead_id);

        return Staff::query()
            ->where('staff_id', $lead->responsible_user_id)
            ->first()
            ->name;
    }

    public function buildTextTg() : string
    {
        $body = implode("\n", [
            'Объект : '.$this->object,
            'Дата и время : '.$this->datetime,
            'Тип встречи : '.$this->type,
            'Имя клиента : '.$this->name,
            'Брокер : '.$this->getStaffName($this->lead_id),
        ]);

        $isNew = ($this->status == 0 && $this->is_new === true) ?  "\n".'#новый' : null;

        return trim($body, "/").$isNew."\n ";
    }

    public function buildTextAmo() : string
    {
        return implode("\n", [
            'Встреча '.$this->matchStatus(),
            '--------------------',
            'Объект : '.$this->object,
            'Дата и время : '.$this->datetime,
            'Тип встречи : '.$this->type,
            'Имя клиента : '.$this->name,
            'Брокер : '.$this->getStaffName($this->lead_id),
        ]);
    }
}
