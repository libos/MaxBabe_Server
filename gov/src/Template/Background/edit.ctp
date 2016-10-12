<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $background->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $background->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Background'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="background form large-10 medium-9 columns">
    <?= $this->Form->create($background); ?>
    <fieldset>
        <legend><?= __('Edit Background') ?></legend>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('filename');
            echo $this->Form->input('path');
            echo $this->Form->input('md5');
            echo $this->Form->input('weather');
            echo $this->Form->input('ge_hour');
            echo $this->Form->input('le_hour');
            echo $this->Form->input('ge_week');
            echo $this->Form->input('le_week');
            echo $this->Form->input('ge_month');
            echo $this->Form->input('le_month');
            echo $this->Form->input('user_id', ['options' => $users, 'empty' => true]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
