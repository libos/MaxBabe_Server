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
                                            <th>UUID</th>
                                            <th>城市名</th>
                                            <th>英文名</th>
                                            <th>地级市</th>
                                            <th>省份</th>
                                            <th>国家</th>
                                            <th>天气来源</th>
                                            <th>AQI来源</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php foreach ($city as $city): ?>
                                                <tr class="odd gradeX">
                                                  <td><?= $city->uuid ?></td>
                                                    <td><?= h($city->name) ?></td>
                                                    <td><?= h($city->pinyin) ?></td>
                                                    <td><?= h($city->level2) ?></td>
                                                    <td><?= h($city->province) ?></td>
                                                    <td><?= h($city->country) ?></td>
                                                    <td><?= h($city->datafrom) ?></td>
                                                    <td><?= h($city->aqifrom) ?></td>
                                                    <td class="actions">
                                                        不能操作
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
