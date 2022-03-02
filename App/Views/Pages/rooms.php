<div class="page-section">
    <h3 class="title">Registro de sala</h3>
    <div class="content">
        <form class="form-ajax" style="display: flex; flex-direction: column" method="POST" action="<?php $this->siteUrl('/api/salas/registrar')?>">
            <input type="text" name="name" class="sys-input" placeholder="Nome da sala">
            <textarea class="sys-input" name="description" placeholder="Descrição da sala"></textarea>
            <div class="action-container">
                <button class="sys-btn">Cadastrar nova sala</button>
            </div>
        </form>

    </div>

</div>

<div class="page-section">
    <h3 class="title">Aplicar filtros á listagem</h3>
    <div class="content">
        <form id="frm-filter" method="GET" action="<?=$this->siteUrl('/api/salas/show')?>">

            <input name="filters[name]" type="text" class="sys-input" placeholder="Nome">
            <input name="filters[avaible_rooms][date]" type="date" class="sys-input">
            <input name="filters[avaible_rooms][start_time]" type="time" class="sys-input">
            <input name="filters[avaible_rooms][end_time]" type="time" class="sys-input">

            <div class="action-container">
                <button class="sys-btn">
                    Aplicar filtros
                </button>
            </div>
        </form>
    </div>
</div>

<div class="page-section">
    <h3 class="title">Listagem das salas</h3>
    <div class="content" id="list-room-content"></div>
</div>