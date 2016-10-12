<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $city->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $city->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List City'), ['action' => 'index']) ?></li>
    </ul>
</div>
<div class="city form large-10 medium-9 columns">
    <?= $this->Form->create($city); ?>
    <fieldset>
        <legend><?= __('Edit City') ?></legend>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('pinyin');
            echo $this->Form->input('level2');
            echo $this->Form->input('province');
            echo $this->Form->input('country');
            echo $this->Form->input('uuid');
            echo $this->Form->input('aqi_uuid');
            echo $this->Form->input('englishname');
            echo $this->Form->input('ext');
            echo $this->Form->input('datafrom');
            echo $this->Form->input('aqifrom');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
