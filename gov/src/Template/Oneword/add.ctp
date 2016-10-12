<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">添加一句话</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<div class="row">
<div class="col-lg-8">
    <div class="panel panel-default">
        <div class="panel-heading">
            <i class="fa fa-bar-chart-o fa-fw"></i> 必填信息
            <div class="pull-right">
            </div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

    <?= $this->Form->create($oneword); ?>
            <div class="form-group input-group">
                <span class="input-group-addon">每行一句话<br>（会<b>自动</b>分割）</span>
                <textarea class="form-control" name="wordss" rows="8" required></textarea>
            </div>
            <div class="form-group input-group">
                <span class="input-group-addon">天气状态@多选<br/><br/>如果选任意天气<br>就不要选别的了</span>
                <?php echo $this->Form->select('weather',$weather_constant,['style'=>'height:200px','class'=>'form-control','default'=>0,'multiple' => true,'label'=>false,'required'=>true]);?>
            </div>
            <?php 
            $opt = array();
            for ($i=0; $i <= 24; $i++) { 
                 $opt[$i] = $i;
            }
            $opt[-10] = "日出";
            $opt[-15] = "日落前（傍晚开始）";
            $opt[-20] = "日落";
            ?>
            <label class="text-danger">在一天内显示的时间</label>
            <div class="row">
                <div class="form-group input-group" style="margin-left:20px;margin-right:40px">
                    <span class="input-group-addon">每天显示@从</span>
                    <?php echo $this->Form->select('ge_hour',$opt,['default'=>-10,'class'=>'form-control','required'=>true]); ?>
                    <span class="input-group-addon">≤点到＜</span>
                    <?php echo $this->Form->select('le_hour',$opt,['default'=>-15,'class'=>'form-control','required'=>true]); ?>
                    <span class="input-group-addon">点显示</span>
                </div>
            </div>
            <?php $opt_week = array('星期一','星期二','星期三','星期四','星期五','星期六','星期日'); ?>
            <label class="text-danger">在一周里哪些天显示</label>
            <div class="row">
                 <div class="form-group input-group" style="margin-left:20px;margin-right:40px">
                     <span class="input-group-addon">每周显示@从</span>
                     <?php echo $this->Form->select('ge_week',$opt_week,['default'=>0,'class'=>'form-control','required'=>true]); ?> 
                     <span class="input-group-addon">&nbsp;&nbsp;到&nbsp;&nbsp;</span>
                        <?php echo $this->Form->select('le_week',$opt_week,['default'=>6,'class'=>'form-control','required'=>true]); ?>
                        <span class="input-group-addon">显示</span>
                </div>
            </div>

            <?php 
            $opt_month = array();
            for ($i=1; $i < 32; $i++) { 
                $opt_month[$i] = $i . "号";
            }
             ?>
            <label class="text-danger">在一月里哪些天显示</label>
            <div class="row">
                <div class="form-group input-group" style="margin-left:20px;margin-right:40px">
                    <span class="input-group-addon">每月显示@从</span>
                    <?php echo $this->Form->select('ge_month',$opt_month,['default'=>0,'class'=>'form-control','required'=>true]); ?>
                    <span class="input-group-addon">&nbsp;&nbsp;到&nbsp;&nbsp;</span>
                    <?php echo $this->Form->select('le_month',$opt_month,['default'=>31,'class'=>'form-control','required'=>true]); ?>
                    <span class="input-group-addon">显示</span>
                </div>
            </div>


            <label class="text-danger">显示温度范围</label>
            <div class="row">
                <div class="form-group input-group" style="margin-left:20px;margin-right:40px">
                    <span class="input-group-addon">温度范围@从</span>
                    <?php echo $this->Form->input('ge_temp',['value'=>-20,'class'=>'form-control','required'=>true,'label'=>false]); ?>
                    <span class="input-group-addon">℃到</span>
                    <?php echo $this->Form->input('le_temp',['value'=>40,'class'=>'form-control','required'=>true,'label'=>false]); ?>
                    <span class="input-group-addon">℃显示</span>
                </div>
            </div>

            <label class="text-danger">显示空气质量指数AQI范围（就是雾霾指数）</label>
            <div class="row">
                <div class="form-group input-group" style="margin-left:20px;margin-right:40px">
                    <span class="input-group-addon">空气质量@从</span>
                    <?php echo $this->Form->input('ge_aqi',['value'=>0,'class'=>'form-control','required'=>true,'label'=>false]); ?>
                    <span class="input-group-addon">到</span>
                    <?php echo $this->Form->input('le_aqi',['value'=>600,'class'=>'form-control','required'=>true,'label'=>false]); ?>
                    <span class="input-group-addon">显示</span>
                </div>
            </div>

         <?= $this->Form->button(__('添加'),['class'=>'btn btn-default']) ?>
        <?= $this->Form->end() ?>

        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div> 

