<?php 
$this->start('css');
echo $this->Html->css('dataTables.bootstrap.css');
echo $this->Html->css('dataTables.responsive.css');
$this->end();
?>
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">添加用户</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<?php 
$opt_week = array('星期一','星期二','星期三','星期四','星期五','星期六','星期日');
$opt_hour = array();
for ($i=0; $i <= 24; $i++) { 
     $opt_hour[$i] = $i;
}
$opt_hour[-10] = "日出";
$opt_hour[-15] = "日落前（傍晚开始）";
$opt_hour[-20] = "日落";
 ?>
          <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            用户列表
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="dataTable_wrapper">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>那句话</th>
                                            <th>天气</th>
                                            <th>≥x小时</th>
                                            <th>＜y小时</th>
                                            <th>≥a周一</th>
                                            <th>≤b周末</th>
                                            <th>≥x月初</th>
                                            <th>≤y月末</th>
                                            <th>≥z℃</th>
                                            <th>≤y℃</th>
                                            <th>≥aqi</th>
                                            <th>≤aqi</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php foreach ($oneword as $oneword): ?>
                                                <tr class="odd gradeX">
                                                    <td><?= h($oneword->word) ?></td>
                                                    <td><?= h($oneword->weather) ?></td>
                                                    <td><?= $opt_hour[$this->Number->format($oneword->ge_hour)] ?></td>
                                                    <td><?= $opt_hour[$this->Number->format($oneword->le_hour)] ?></td>
                                                    <td><?= $opt_week[$oneword->ge_week] ?></td>
                                                    <td><?= $opt_week[$oneword->le_week] ?></td>
                                                    <td><?= $oneword->ge_month . "号"?></td>
                                                    <td><?= $oneword->le_month . "号"?></td>
                                                    <td><?= $oneword->ge_temp . "℃"?></td>
                                                    <td><?= $oneword->le_temp . "℃"?></td>
                                                    <td><?= $oneword->ge_aqi ?></td>
                                                    <td><?= $oneword->le_aqi ?></td>
                                                    <td class="actions">
                                       <!--                  <?= $this->Html->link(__('详情'), ['action' => 'view', $oneword->id]) ?>-->
                                                        <?= $this->Html->link(__('编辑'), ['action' => 'edit', $oneword->id]) ?> <br/><br/>
                                                        <?= $this->Form->postLink(__('删除'), ['action' => 'delete', $oneword->id], ['confirm' => __('Are you sure you want to delete # {0}?', $oneword->id)]) ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="paginator" style="float:right">
                                <ul class="pagination">
                                    <?= $this->Paginator->prev('< ' . __('上一页')) ?>
                                    <?= $this->Paginator->numbers() ?>
                                    <?= $this->Paginator->next(__('下一页') . ' >') ?>
                                </ul>
                            </div>
                        </div>

                        <!-- /.panel-body -->
                    </div>
                    
                    <!-- /.panel -->
                </div>

                <!-- /.col-lg-12 -->
            </div>


<?php $this->start('script'); ?>
<?php 
echo $this->Html->script('jquery.dataTables.min.js');
echo $this->Html->script('dataTables.bootstrap.min.js');
 ?>
<script>
$(document).ready(function() {
    $('#dataTables-example').DataTable({
            responsive: true,
            paging: false,
            searching: false,
            info: false
    });
});
</script>
<?php $this->end(); ?>

