<?php 
$this->start('css');
echo $this->Html->css('blueimp-gallery.min.css');

echo $this->Html->css('jquery.fileupload.css');
echo $this->Html->css('jquery.fileupload-ui.css');


$this->end();?>


<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">添加形象图</h1>
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

    <?= $this->Form->create($figure,['enctype' => 'multipart/form-data','class'=>'xfileupload']); ?>
            <div class="form-group input-group">
                    <span class="input-group-addon">形象系列名（这个名字其实没啥用，但是必填）</span>
                    <?php echo $this->Form->input('name',['class'=>'form-control','label'=>false,'required'=>true,'placeholder'=>'任性']); ?>
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

            <label>分辨率选择</label>
            <div class="row">
                 <div class="form-group input-group" style="margin-left:20px;margin-right:40px">
                     <span class="input-group-addon">分辨率选择@</span>
                     <?php echo $this->Form->select('reso',array("xx"=>"1920*1080","x"=>"1280x720"),['default'=>'xx','class'=>'form-control','required'=>true]); ?> 
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    上传文件
                </div>
                    <div id="hidden_input">
                        <input type="hidden" name="filecount" id="filecount" value=0>
                    </div>
                <div class="panel-body">

                        <div class="row fileupload-buttonbar">
                            <div class="col-lg-9">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                <span class="btn btn-success fileinput-button">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    <span>添加文件...</span>
                                    <input type="file" name="files[]" multiple >
                                </span>
                       <!--          <button type="button" class="btn btn-primary start">
                                    <i class="glyphicon glyphicon-upload"></i>
                                    <span>开始上传</span>
                                </button>
                                <button type="reset" class="btn btn-warning cancel">
                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                    <span>取消上传</span>
                                </button>
                                <button type="button" class="btn btn-danger delete">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    <span>删除</span>
                                </button>
                                <input type="checkbox" class="toggle"> 全选
                             -->    <!-- The global file processing state -->
                                <span class="fileupload-process"></span>
                            </div>
                            <!-- The global progress state -->
                            <div class="col-lg-5 fileupload-progress fade">
                                <!-- The global progress bar -->
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                                <!-- The extended global progress state -->
                                <div class="progress-extended">&nbsp;</div>
                            </div>
                        </div>
                        <!-- The table listing the files available for upload/download -->
                        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>

                </div>
                <!-- /.panel-body -->
            </div>


         <?= $this->Form->button(__('添加'),['class'=>'btn btn-default']) ?>
        <?= $this->Form->end() ?>

        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div> 
<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">上传中...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>开始上传</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>取消上传</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
               <p></p>
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
 <!-- // <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                //     <i class="glyphicon glyphicon-trash"></i>
                //     <span>删除</span>
                // </button>
                // <input type="checkbox" name="delete" value="1" class="toggle"> -->
<?php $this->start('script'); ?>
<?php 
echo $this->Html->script('jquery.ui.widget.js');
echo $this->Html->script('tmpl.min.js');
echo $this->Html->script('load-image.all.min.js');
echo $this->Html->script('canvas-to-blob.min.js');
echo $this->Html->script('jquery.blueimp-gallery.min.js');
echo $this->Html->script('jquery.iframe-transport.js');
echo $this->Html->script("jquery.fileupload.js");
echo $this->Html->script("jquery.fileupload-process.js");
echo $this->Html->script("jquery.fileupload-image.js");
echo $this->Html->script("jquery.fileupload-audio.js");
echo $this->Html->script("jquery.fileupload-video.js");
echo $this->Html->script("jquery.fileupload-validate.js");
echo $this->Html->script("jquery.fileupload-ui.js");
echo $this->Html->script("upload_figure.js");
?>
<!--[if (gte IE 8)&(lt IE 10)]>
<?php echo $this->Html->script("jquery.xdr-transport.js"); ?><script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->


<?php $this->end(); ?>
