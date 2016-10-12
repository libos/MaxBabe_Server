<div class="actions columns large-2 medium-3">
    <h3><?= __('Actions') ?></h3>
    <ul class="side-nav">
        <li><?= $this->Html->link(__('Edit Background'), ['action' => 'edit', $background->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Background'), ['action' => 'delete', $background->id], ['confirm' => __('Are you sure you want to delete # {0}?', $background->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Background'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Background'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
    </ul>
</div>
<div class="background view large-10 medium-9 columns">
    <h2><?= h($background->name) ?></h2>
    <div class="row">
        <div class="large-5 columns strings">
            <h6 class="subheader"><?= __('Name') ?></h6>
            <p><?= h($background->name) ?></p>
            <h6 class="subheader"><?= __('Filename') ?></h6>
            <p><?= h($background->filename) ?></p>
            <h6 class="subheader"><?= __('Path') ?></h6>
            <p><?= h($background->path) ?></p>
            <h6 class="subheader"><?= __('Md5') ?></h6>
            <p><?= h($background->md5) ?></p>
            <h6 class="subheader"><?= __('Weather') ?></h6>
            <p><?= h($background->weather) ?></p>
            <h6 class="subheader"><?= __('User') ?></h6>
            <p><?= $background->has('user') ? $this->Html->link($background->user->id, ['controller' => 'Users', 'action' => 'view', $background->user->id]) : '' ?></p>
        </div>
        <div class="large-2 columns numbers end">
            <h6 class="subheader"><?= __('Id') ?></h6>
            <p><?= $this->Number->format($background->id) ?></p>
            <h6 class="subheader"><?= __('Ge Hour') ?></h6>
            <p><?= $this->Number->format($background->ge_hour) ?></p>
            <h6 class="subheader"><?= __('Le Hour') ?></h6>
            <p><?= $this->Number->format($background->le_hour) ?></p>
            <h6 class="subheader"><?= __('Ge Week') ?></h6>
            <p><?= $this->Number->format($background->ge_week) ?></p>
            <h6 class="subheader"><?= __('Le Week') ?></h6>
            <p><?= $this->Number->format($background->le_week) ?></p>
            <h6 class="subheader"><?= __('Ge Month') ?></h6>
            <p><?= $this->Number->format($background->ge_month) ?></p>
            <h6 class="subheader"><?= __('Le Month') ?></h6>
            <p><?= $this->Number->format($background->le_month) ?></p>
        </div>
        <div class="large-2 columns dates end">
            <h6 class="subheader"><?= __('Created') ?></h6>
            <p><?= h($background->created) ?></p>
        </div>
    </div>
</div>
