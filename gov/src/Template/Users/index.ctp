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
                                            <th>ID</th>
                                            <th>邮箱</th>
                                            <th>注册时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <?php foreach ($users as $user): ?>
                                                <tr class="odd gradeX">
                                                    <td class="center"><?= $this->Number->format($user->id) ?></td>
                                                    <td><?= h($user->email) ?></td>
                                                    <td><?= h($user->created) ?></td>
                                                    <td class="center" >
                                                        <?= $this->Html->link(__('编辑'), ['action' => 'edit', $user->id]) ?>
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