<?php
include "../plugin/simple_html_dom.php";
$arr = array(	"http://www.weather.com.cn/textFC/beijing.shtml",
				"http://www.weather.com.cn/textFC/anhui.shtml",
				"http://www.weather.com.cn/textFC/chongqing.shtml",
				"http://www.weather.com.cn/textFC/fujian.shtml",
				"http://www.weather.com.cn/textFC/gansu.shtml",
				"http://www.weather.com.cn/textFC/guangdong.shtml",
				"http://www.weather.com.cn/textFC/guangxi.shtml",
				"http://www.weather.com.cn/textFC/guizhou.shtml",
				"http://www.weather.com.cn/textFC/hainan.shtml",
				"http://www.weather.com.cn/textFC/hebei.shtml",
				"http://www.weather.com.cn/textFC/henan.shtml",
				"http://www.weather.com.cn/textFC/hubei.shtml",
				"http://www.weather.com.cn/textFC/hunan.shtml",
				"http://www.weather.com.cn/textFC/heilongjiang.shtml",
				"http://www.weather.com.cn/textFC/jilin.shtml",
				"http://www.weather.com.cn/textFC/jiangsu.shtml",
				"http://www.weather.com.cn/textFC/jiangxi.shtml",
				"http://www.weather.com.cn/textFC/liaoning.shtml",
				"http://www.weather.com.cn/textFC/neimenggu.shtml",
				"http://www.weather.com.cn/textFC/ningxia.shtml",
				"http://www.weather.com.cn/textFC/qinghai.shtml",
				"http://www.weather.com.cn/textFC/shandong.shtml",
				"http://www.weather.com.cn/textFC/shan-xi.shtml",
				"http://www.weather.com.cn/textFC/shanxi.shtml",
				"http://www.weather.com.cn/textFC/shanghai.shtml",
				"http://www.weather.com.cn/textFC/sichuan.shtml",
				"http://www.weather.com.cn/textFC/tianjin.shtml",
				"http://www.weather.com.cn/textFC/xizang.shtml",
				"http://www.weather.com.cn/textFC/xinjiang.shtml",
				"http://www.weather.com.cn/textFC/yunnan.shtml",
				"http://www.weather.com.cn/textFC/zhejiang.shtml",
				"http://www.weather.com.cn/textFC/hongkong.shtml",
				"http://www.weather.com.cn/textFC/macao.shtml",
				"http://www.weather.com.cn/textFC/taiwan.shtml");


	foreach ($arr as $idx => $url) {
		$html = file_get_html($url);
		foreach($html->find('.conMidtab',0)->find('table tr a') as $ele) 
		{
			$text = $ele->plaintext;
			if($text != "详情")
			{
				$href = $ele->href;
				$cid = basename($href,'.shtml');
				
				echo '"' . $text . '"=>"'. $cid . '",'."\n";
			}
		}
//		break;
	}


?>
