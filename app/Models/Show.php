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

    //TODO
    public function matchChatId() : string
    {
        return match ($this->pipeline_id) {

            0  => '-4075342320',
            1  => '-4075342320',
            2  => '-4075342320',
            default  => '-4075342320',
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
