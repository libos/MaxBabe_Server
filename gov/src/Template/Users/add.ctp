<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">添加用户</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
<div class="col-lg-8">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bar-chart-o fa-fw"></i> 必填信息
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
                <?= $this->Form->create($user); ?>
                <div class="form-group input-group">
                    <span class="input-group-addon">邮箱</span>
                    <?php echo $this->Form->input('email',['class'=>'form-control','label'=>false,'required'=>true]); ?>
                </div>
                <div class="form-group input-group">
                    <span class="input-group-addon">密码</span>
                    <?php echo $this->Form->input('password',['class'=>'form-control','label'=>false,'required'=>true]);?>
                </div>
                <?= $this->Form->button(__('添加'),['class'=>'btn btn-default']) ?>
                <?= $this->Form->end() ?>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div> 






