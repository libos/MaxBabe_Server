<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
?>

<div class="alert alert-info alert-dismissable" style="margin-top:20px">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<div class="<?= h($class) ?>"><?= h($message) ?></div>
</div>