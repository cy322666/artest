<div class="container">
    <div class="d-flex align-items-center min-vh-100">
        <div class="w-60 mx-auto">
            <form wire:submit.prevent="save">
                <div class="text-center mb-4">
                    <h1 class="h3 mb-3 font-weight-normal">Форма показа</h1>
                    <p>Изменяйте показ заполнив форму</p>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input type="text" wire:model="object" class="form-control" placeholder="Объект" required {{ $isClose ? 'disabled' : ''}}>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input type="text" wire:model="type" class="form-control" placeholder="Тип показа" required {{ $isClose ? 'disabled' : ''}}>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input type="datetime-local" wire:model="datetime" class="form-control" placeholder="Дата и время" required {{ $isClose ? 'disabled' : ''}}>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <select class="form-select" wire:model="status" aria-label="Статус показа" required {{ $isClose ? 'disabled' : ''}}>
                            <option value=0>Записан</option>
                            <option value=1>Перенесен</option>
                            <option value=2>Проведен</option>
                            <option value=3>Отменен</option>
                        </select>
                    </div>
                </div>

                    <div class="form-row text-center">
                        <div class="col-12">
                            <button class="form-row text-center btn btn-sl btn-primary btn-block" {{ $isClose ? 'disabled' : ''}} type="submit">Изменить</button>
                        </div>

                        <div class="col-12">
                            <a class="text-center btn btn-sl btn-link btn-block" href="https://{{ env('AMOCRM_SUBDOMAIN') }}/amocrm.ru/leads/detail/{{ $show->id }}">В сделку</a>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</div>
