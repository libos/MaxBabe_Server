<!DOCTYPE html>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
     <title>麦宝 - Maxtain Ltd.</title>
    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->css('gov.css') ?>


</head>
<body>
<div class="container">
<div id="error" class="alert alert-danger alert-dismissable" style="margin-top:20px;display:none;">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
<p id="err_msg"></p>
</div>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div id="panel" class="login-panel panel panel-default">
                <?php if ($is_right!="false"): ?><div class="panel-heading">
                    <h3 class="panel-title"><?php echo $email ?>找回密码</h3>
                </div>
                <?php endif ?>        
                <div class="panel-body">
                <?php if ($is_right !="false"): ?>
                    <form role="form" action='#' method='post'>
                        <fieldset>
                            <div class="form-group">
                                <input class="form-control" id="pass" placeholder="Password" name="password" type="password" value="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" id="pass_con" placeholder="Password Confirm" name="password_confirm" type="password" value="">
                            </div>
                            <input type="hidden" name="email" value="<?php echo $email ?>">
                            <input type="hidden" name="vstr" value="<?php echo $hash ?>">
                            <button type="submit"  class="btn btn-lg btn-success btn-block">修改密码</button>
                        </fieldset>
                    </form>
                <?php else: ?>
                    <div>非法请求-.-b!</div>
                <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->Html->script('jquery.min.js') ?>
<?= $this->Html->script('bootstrap.min.js') ?>

<script type="text/javascript">
    $('form').submit(function (e) {
        e.preventDefault();
        if ($('#pass').val().length < 6 || $('#pass').val().length > 36) {
            $('#err_msg').html("密码太长或太短");
            $('#error').show();
            setTimeout('$("#error").fadeOut()',3000);
            return;
        };
        if ($('#pass').val() != $('#pass_con').val()) {
            $('#err_msg').html("两次密码输入不正确");
            $('#error').show();
            setTimeout('$("#error").fadeOut()',3000);
            return;
        };

        $.ajax({
            url:'/getpass.php',
            dataType:'json',
            type:'POST',
            data:{'email':$('input[name=email]').val(),'vstr':$('input[name=vstr]').val(),'password':$('#pass').val(),'password_confirm':$('#pass_con').val()}
        }).done(function(json) {
            if (json.state == "done") {
                $('#panel').html('');
                $('#err_msg').html("变更密码成功！");
                $('#error').attr('class','alert alert-success alert-dismissable');
                $('#error').show();
            }else if (json.state == "illegal request") {
                $('#err_msg').html("非法请求-.-b！");
                $('#error').show();
            }else if (json.state == "password") {
                $('#err_msg').html("密码长度需要大于6位，小于36位");
                $('#error').show();
            }else{
                $('#err_msg').html("某个地方有问题，你确定你是地球人？");
                $('#error').show();
            }
        });

    });

</script>
</body>
</html>