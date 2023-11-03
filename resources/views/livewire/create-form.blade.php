<div class="container">
    <div class="d-flex align-items-center min-vh-100">
        <div class="w-60 mx-auto">

            <form wire:submit.prevent="save">
                <div class="text-center mb-4">

                    <h1 class="h3 mb-3 font-weight-normal">Форма показа</h1>
                    <p>Создавайте показ заполнив форму</p>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input wire:model="object" type="text" class="form-control" placeholder="Объект" required="" autofocus="">
                    </div>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input wire:model="type" type="text" class="form-control" placeholder="Тип показа" required="">
                    </div>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input wire:model="phone" type="text" class="form-control" placeholder="Телефон" required="">
                    </div>
                </div>

                <div class="mb-2">
                    <div class="form-label-group">
                        <input wire:model="datetime" type="datetime-local" class="form-control" placeholder="Дата и время" required="">
                    </div>
                </div>

                <div class="form-row text-center">
                    <div class="col-12">
                        <button class="form-row text-center btn btn-sl btn-primary btn-block" type="submit">Создать</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
