
<div class="page-section">
    <h3 class="title">Agendamento de sala</h3>
    <div class="content">
        <?php if (!empty($rooms)): ?>
        <form class="form-ajax" style="display: flex; flex-direction: column" method="POST" action="<?php $this->siteUrl('/api/salas/agendar')?>">
            <input type="date" name="date" placeholder="Data" class="sys-input" value="<?=date('Y-m-d')?>">
            <input type="time" name="start_time" placeholder="Hora inicial" class="sys-input" value="00:00">
            <input type="time" name="end_time" placeholder="Hora final" class="sys-input" value="00:00">

            <select name="id_room"  class="sys-input">

                    <?php foreach ($rooms as $roomEntity):?>
                    <option value="<?=$roomEntity->getId()?>">#<?=$roomEntity->getId()?> - <?=$roomEntity->getName()?></option>
                    <?php endforeach;?>

            </select>
            <div class="action-container">
                <button class="sys-btn">Agendar sala</button>
            </div>
        </form>
        <?php else:?>
        <p class="message warning">Você precisa registrar ao menos uma sala para poder realizar agendamentos!</p>
        <?php endif;?>

    </div>

</div>


<div class="page-section">
    <h3 class="title">Agendamentos</h3>
    <div class="content" id="list-scheduling-content">
    </div>
</div>