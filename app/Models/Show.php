<?php

namespace App\Models;

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
    ];

    public function matchStatus() : string
    {
        return match ((int)$this->status) {

            0 => 'запланирована',
            1 => 'проведена',
            2 => 'перенесена',
            3 => 'отменена',
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

    public function buildTextTg() : string
    {
        $title = '*Встреча '.$this->matchStatus()."*\n";

        $body = implode("/", [
            $this->datetime,
            $this->object,
            $this->type,
            $this->name,
        ]);

        $isNew = ($this->status == 0 && $this->is_new === true) ?  '#новый' : null;

        return $title. trim($body, "/").$isNew."\n ";
    }

    public function buildTextAmo() : string
    {
        return implode("\n", [
            'Встреча '.$this->matchStatus(),
            '--------------------',
            $this->datetime,
            $this->object,
            $this->type,
            $this->name,
        ]);
    }
}
