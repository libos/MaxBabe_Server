<?php
require_once '/alidata/www/BebeServ/api/users/Mailer/SMTP.php';
require_once '/alidata/www/BebeServ/api/users/Mailer/Message.php';

use \Tx\Mailer\SMTP;
use \Tx\Mailer\Message;
date_default_timezone_set("Asia/Harbin");
$loft = date("Y年m月d日");
$nickname = $_GET['nickname'];
$hash = $_GET['hash'];
$email = $_GET['email'];
$mail = <<<EOD
<h1 style="background-color:#4bb9d2; color:#ffffff; line-height:40px; font-size:20px; text-align:center; ">麦宝星 Maxbabe</h1>
<p style="margin: 0px; padding: 0px; clear: both; text-align:center;">
<strong style="font-size: 14px; line-height: 24px; color: rgb(51, 51, 51); font-family: arial, sans-serif;">
亲爱的用户，您好
</strong>
</p >
<p style="margin: 0px; padding: 0px; line-height: 24px; clear: both; text-align:center;">
<span style="font-size: 12px; color: rgb(51, 51, 51); font-family: 宋体, arial, sans-serif;">
您提交了邮箱找回密码请求，请点击下面的链接修改密码。
</span>
</p >
<p style="margin: 0px; padding: 0px; clear: both;">
<a href="http://babe.maxtain.com/getpassresetpwd?vstr={$hash}&email={$email}" target="_blank" style="line-height: 24px; font-size: 16px; font-family: arial, sans-serif; color: rgb(0, 0, 204); border:1px solid #4bb9d2; padding:20px 40px; display:block; width:200px; text-align:center; border-radius:4px; margin:20px auto; color:#ffffff; background-color:#4bb9d2; text-decoration:none;">
重设密码
</a >
</p>
<p style="margin: 0px; padding: 0px; line-height: 24px; clear: both; text-align:center;"><span style="font-size: 12px; color: rgb(151, 151, 151); font-family: arial, sans-serif;">
(如果您无法点击此链接，请将它复制到浏览器地址栏后访问)
</span></p >
<p style="text-align:center;">
<a href="http://babe.maxtain.com/getpassresetpwd?vstr={$hash}&email={$email}">http://babe.maxtain.com/getpassresetpwd?vstr={$hash}&email={$email}
</a>
</p>
<p style="margin: 0px; padding: 0px; line-height: 24px; clear: both;text-align:center;"><span style="font-size: 12px; color: rgb(51, 51, 51); font-family: 宋体, arial, sans-serif;">
为了保证您帐号的安全，该链接有效期为24小时，并且点击一次后失效！
</span>
</p >
<p style="margin: 0px; padding: 0px; line-height: 24px; clear: both; text-align:center;"><span style="font-size: 12px; color: rgb(51, 51, 51); font-family: 宋体, arial, sans-serif;">
Maxtain团队
</span>
</p >
EOD;
$smtp = new SMTP();
$smtp->setServer("smtp.exmail.qq.com", "465", "ssl")->setAuth('info@maxtain.com', '1qazXSW@');
$message = new Message();
$message->setFrom('麦宝星MaxBabe', 'info@maxtain.com')->setTo($nickname, $email)->setSubject('重置密码 Reset Password')->setBody($mail);
$smtp->send($message);
