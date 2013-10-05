<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8" />
<title>AC Find——查找A站源视频、一键下载视频、转换弹幕 Beta1.1</title>
<style>
#nav_left {
    height: 510px;
    width: 90px;
    margin-left:-90px;
    opacity: 0.1;
    filter:alpha(opacity:10);
    position: absolute;
    text-align: center;
    cursor: pointer;
    transition: .2s;
    -webkit-transition: .2s;
    -moz-transition: .2s;
    -o-transition: .2s;
}
#nav_right {
    position: absolute;
    height: 510px;
    width: 90px;
    top: 10px;
    opacity: 0.1;
    filter:alpha(opacity:10);
    text-align: center;
    cursor: pointer;
    margin-top: 50px;
    margin-left: 860px;
    transition: .2s;
    -webkit-transition: .2s;
    -moz-transition: .2s;
    -o-transition: .2s;
}
#nav_left:hover {
    background-color: rgba(0, 0, 0, 0.22); 
    opacity: 1;
    filter:alpha(opacity:100);
}
#nav_right:hover {
    background-color: rgba(0, 0, 0, 0.22); 
    opacity: 1;
    filter:alpha(opacity:100);
}
.nav_mark_left {
	top: 46%;
	position: absolute;
	text-align: center;
	font-size: 60px;
}
.nav_mark_right {
	top: 42%;
	position: relative;
	text-align: center;
	font-size: 60px;
	margin-left:30px;
    margin-top: 20px;
}
#dll2 {
	visibility: hidden;
	position: absolute;
}
.acfun {
    display:block;
    margin:0 auto;
    width:860px;
}
</style>
<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript" src="js/jquery.powertip.js"></script>
<script type="text/javascript" src="js/core.js"></script>
<script type="text/javascript">
	var y=document.getElementsByClassName("dll");
	for(i=0;i<y.length;i++){
	if(y[i].value==''){
		y[i].style.display='none';
		}
	}
	document.ready(function(){
  		var iTitle=document.getElementById("dll2");
			document.title=iTitle.innerHTML;
	});
</script>
<link type="text/css" rel="stylesheet" href="css/bootstrap.css" />
<link type="text/css" rel="stylesheet" href="css/main.css" />
<link type="text/css" rel="stylesheet"  href="css/jquery.powertip.css" />
<link type="text/css" rel="stylesheet" href="css/index.css" />

<!--弹出层-->
<link href="css/bootstrap-modal.css" rel="stylesheet" />
<script src="js/bootstrap.js"></script>
<script src="js/bootstrap-modalmanager.js"></script>
<script src="js/bootstrap-modal.js"></script>
</head>

<body>
<?php
include('simple_html_dom.php');
function curl_data($url,$data){
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $tmpInfo = curl_exec($curl);
    curl_close($curl);
    return $tmpInfo;
}

function extracts($source,$video_url){
	if($source=='qq'){
		preg_match('/http:\/\/(.*)html/', $video_url, $ass);
		if($ass==''){
			return $video_url;
		}else{
			$aaa=basename($video_url,'.html');
			if(preg_match('/html/',$aaa)){
				preg_match('/vid=(.*)&/U', $aaa,$bbb);
                if($bbb[1]==''){
                	preg_match('/vid=(.*)/', $aaa,$bbb);
                }
				return 'http://static.video.qq.com/TPout.swf?vid='.$bbb[1].'';
			}else{
				return 'http://static.video.qq.com/TPout.swf?vid='.$aaa.'';
			}
		}
	}else
	/*处理老视频*/
	if($source=='video.sina'){
		if(preg_match("/^[\d]{5,15}$/",$video_url)){
			return $video_url;
		}else{
		preg_match('/cn\/([a-z]?)\/([a-z]?)\/(.*).html/',$video_url,$attr);
		$a=preg_replace('/\-/','_',$attr[3]);
		return $a;
			}
		}else
	if($source=='you.video.sina'){
		preg_match('/cn\/([a-z]?)\/(.*).html/',$video_url,$attr);
		$a=preg_replace('/\-/','_',$attr[2]);
		return $a;
		}else
	if($source=='pps'){
		preg_match('/play_(.*).html/',$video_url,$attr);
		if(@$attr[1]==''){
			return 'http://player.pps.tv/player/sid/'.$video_url.'/v.swf';
		}else{
			return 'http://player.pps.tv/player/sid/'.$attr[1].'/v.swf';
			}
		}else
	if($source=='v.youku'){
		preg_match('/id_(.*).html/',$video_url,$attr);
		if(@$attr[1]==''){
			return $video_url;
		}else{
			return $attr[1];
			}
		}else
	if($source=='www.tudou'){
		preg_match('/view\/(.*)\//',$video_url,$attr);
		if(!isset($attr[1])){
			$attr_a=basename($video_url,'.html');
			return $attr_a;
		}else{
			return $attr[1];
		}
		}else{
			return $attr[1];
			}
}

function download($url){
    	$s = new SaeStorage();
		$folder = '/';
		$dl_list_name = $folder.basename($_POST['url']).'.lst';
		$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$url.'');
    	//$names=fopen($dl_list_name, 'w+');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                    $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
				echo '<textarea class="dll">';
				foreach($html->find('a[onclick=_alert();return false;]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
    			//fclose($names);
}
function download_youku($video_url){
			$s = new SaeStorage();
			$folder = '/';
			$dl_list_name = $folder.basename($_POST['url']).'.lst';
			if(preg_match('/id_(.*).html/',$video_url)){
					$cc=urlencode($video_url);
				}else{
					$cc='http://v.youku.com/v_show/id_'.$video_url.'.html';
				}
				$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$cc.'&flag=one&format=super');
    			//$names=fopen($dl_list_name, 'w+');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
				echo '<textarea class="dll">';
				foreach($html->find('a[onclick=_alert();return false;]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
    //fclose($names);
}
function download_tudou($url){
	$s = new SaeStorage();
	$folder = '/';
	$dl_list_name = $folder.basename($_POST['url']).'.lst';
	$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$url.'&flag=one&format=real');
    //$names=fopen($dl_list_name, 'w+');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
				echo '<textarea class="dll">';
				foreach($html->find('a[onclick=_alert();return false;]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
    //fclose($names);
}
function download_pps($video_url){
			$s = new SaeStorage();
			$folder = '/';
			$dl_list_name = $folder.basename($_POST['url']).'.lst';
			if(preg_match('/play_(.*).html/',$video_url)){
					$cc=urlencode($video_url);
				}else{
					$cc='http://v.pps.tv/play_'.$video_url.'.html';
				}
				$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$cc.'&flag=one&format=high');
    //$names=fopen($dl_list_name, 'w+');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
				echo '<textarea class="dll">';
				foreach($html->find('a[onclick=_alert();return false;]') as $e){
					$names1 = $e->outertext;
					preg_match('/href=\"(.*)\"\starget/U', $names1,$names2);
					if(!empty($names2[1])){
                        //fwrite($names, $names2[1]."\r");
                        $s->write('download' , $dl_list_name, $names2[1]."\r");
					}
					echo $e->outertext;
					}
				echo '</textarea>';
    //fclose($names);
}
	/*
			if($source=='qq'){
				return '<font color=red>腾讯视频暂不提供下载，如果你有获取腾讯视频真实地址的方法，欢迎通过底部的邮箱链接联系我:D</font>';
			}else
			if($source=='www.tudou'){
				$ccc=urlencode($video_url);
				$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$ccc.'');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					echo $e->outertext;
					}
				echo '</textarea>';
			}else
			if($source=='v.youku'){
				if(preg_match('/id_(.*).html/',$video_url)){
					$cc=urlencode($video_url);
				}else{
					$cc='http://v.youku.com/v_show/id_'.$video_url.'.html';
				}
				$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$cc.'');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					echo $e->outertext;
					}
				echo '</textarea>';
				echo '<textarea class="dll">';
				foreach($html->find('a[onclick=_alert();return false;]') as $e){
					echo $e->outertext;
					}
				echo '</textarea>';
			}else
			if($source=='video.sina'||$source=='you.video.sina'){
				if(preg_match("/^[\d]{5,15}$/",$video_url)){
					echo '<textarea class="dll"><p class="text-error">该视频暂时无法获取源，如果你知道如何只通过Ac返回的vid得到新浪源地址，请!联!系!我!→admin#lolimilk.com</p></textarea>';
				}else{
				$ccc=urlencode($video_url);
				$html = file_get_html('http://www.flvcd.com/parse.php?format=&kw='.$ccc.'');
				echo '<textarea class="dll">';
				foreach($html->find('a[class=link]') as $e){
					echo $e->outertext;
					}
				echo '</textarea>';
				echo '<textarea class="dll">';
				foreach($html->find('a[onclick=_alert();return false;]') as $e){
					echo $e->outertext;
					}
				echo '</textarea>';*/
				/*foreach($html->find('font[color=red]') as $f){
					echo $f->outertext;
					}*/
				/*
				//$ran = rand(0,1000);
				$ran = 0; //一个随机数 as3 用 Math.random() 生成
				//$time = time();
				//$time = decbin($time);
				//$time = substr($time, 0, -6);
				//$time = bindec($time);
				$time = 0; //一个时间戳，如果失效 用上面4行代码
        $a = array(
            $vid,
            'Z6prk18aWxP278cVAH', 
            $time,
            $ran,
        );
        $key = implode('', $a);
        $key = md5($key);
        $key = substr($key, 0, 16);
        $key .= $time;
            
        $xml_url = 'http://v.iask.com/v_play.php?' . http_build_query(array(
                'vid' => $vid,
                'uid' => 'null',
                'pid' => 'null',
                'tid' => 'undefined',
                'plid' => '4001',
                'prid' => 'ja_7_4993252847',
                'referrer' => '',
                'ran' => $ran,
                'r' => 'video.sina.com.cn',
                'v' => 'p2p4.1.42.23',
                'p' => 'i',
                'k' => $key,
            ));
			事例：http://v.iask.com/v_play.php?vid=110860064&uid=null&pid=null&tid=undefined&plid=4001&prid=ja_7_4993252847&referrer=&ran=0.5&r=video.sina.com.cn&v=p2p4.1.42.23&p=i&k=72fb3c4c996254510
				
				$sinavid=basename($video_url,'.html');
				if(preg_match("/^[\d]{5,15}$/",$video_url)){
					$vid=$video_url;
				}else{
					$viduid=(explode('-',$sinavid));
					$vid =  $viduid[0];
				}
				$dom=new DomDocument();   //创建DOM对象
				$dom->load('http://v.iask.com/v_play.php?vid='.$vid.'&uid=null&pid=null&tid=undefined&plid=4001&prid=ja_7_4993252847&referrer=&ran=0.5&r=video.sina.com.cn&v=p2p4.1.42.23&p=i&k=72fb3c4c996254510');  //获取xml文件
				$content = $dom->textContent;
				//echo $content;
				preg_match_all('/http:\/\/edge\.v\.iask\.com\/([0-9]*)\.hlv\?KID=sina,viask&Expires=([0-9]*)&ssig=.{5,15}/',$content,$dl_url);
				$dl_url_length=count($dl_url[0]);
				for($i=0;$i<$dl_url_length;$i++){
					return ($i+1).'、<a href="'.$dl_url[0][$i].'">第'.($i+1).'片段</a><br>';
				}
				preg_match('/http:\/\/(.*)\s{0,1}/',$content,$dl_url);
				print_r($dl_url);
				*/
				//}
			//}

/*判断是否为A站地址*/
function is_ac($str){
	return preg_match("/^http:\/\/www\.acfun\.tv\/v\/ac([0-9]*)/", $str);
}
if($_POST['errorurl']){
    $ac_url=basename($_POST['errorurl']);
    echo '<div class="titles"><a href="'.$url.'" target="_blank" title="点我回到主站对应视频" class="backtoac">'.$up_title.'</a></div>';
    echo '
    <div class="acfun">
    <object id="ac" name="obj1" allowfullscreen="true" style="visibility: visible;display: block;margin: 0 auto;margin-top: -524px;margin-top: 20px;box-shadow: 0 0 15px black;-webkit-box-shadow: 0 0 15px black;-moz-box-shadow: 0 0 15px black;-o-box-shadow: 0 0 15px black;" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="860" height="510">
				<param name="movie" value="http://w5cdn.ranktv.cn/player/ACFlashPlayerX.out.20130927.swf?type=page&url='.$ac_url.'" />
				<param name="allowFullScreen" value="true" />
                <param name="allowFullscreenInteractive" value="true">
                <param name="allowscriptaccess" value="always"><param name="flashvars" value="lcid=07452844260260463&width=1&height=1&wmode=direct&allowFullscreenInteractive=true&allowfullscreen=true&allowscriptaccess=always">
        		<!--[if !IE]>-->
				<object id="ac1" type="application/x-shockwave-flash" data="http://w5cdn.ranktv.cn/player/ACFlashPlayerX.out.20130927.swf?type=page&url='.$ac_url.'" width="860" height="510">
				<!--<![endif]-->
				<div>
					<h1>Acfind</h1>
				</div>
				<!--[if !IE]>-->
				</object>
				<!--<![endif]-->
			</object>
    </div>
    ';

}
if(@$_POST['url']){
	  $url=$_POST['url']; //得到ac地址
      preg_match('/http(.*)/',$url,$acnum);
      if($acnum[1]==''){
    	$url='http://www.acfun.tv/v/'.$url;
      }
	  $url_encode=urlencode($url);
	  $baseurl=basename($url);
	  if(preg_match('/_/',$baseurl)){
	  	preg_match('/_([0-9]*)/',$baseurl,$temp);
	  	$temp_prev = $temp[1]-1;
	  	$temp_next = $temp[1]+1;
	  	preg_match('/http(.*)_/',$url,$temp_1);
	  	$prev = 'http'.$temp_1[1].'_'.$temp_prev;
	  	$next = 'http'.$temp_1[1].'_'.$temp_next;
	  	//echo $prev.'<br>';
	  	//echo $next;
	  }else{
	  	$temp_prev = '1';
	  	$temp_next = '2';
	  	$prev = $url.'_'.$temp_prev;
	  	$next = $url.'_'.$temp_next;
	  }
	  $bb=is_ac($url);
	  if(!$bb){
		  echo '<script>alert("这不是A站的地址吧少年哟");</script>
          		<center><a href="javascript:document.location.reload();"><img src="image/error.png" style="height:646px;width:438px;"></a></center>
          		';
		  }else
		  {  
	  $html = file_get_html($url);  //加载类
	  $q= @$html->plaintext;  //提取网页文字
	  
	  preg_match('/\[video\](.*)\[\/video\]/i', $q, $arr); //匹配VID，VID:$arr[1]
	  preg_match('/\/ac(.*)/',$url,$fanhao);  //匹配番号，$fanhao[1]
	
	  if(isset($arr[1])){
	  $redirect_url = 'http://www.acfun.tv/api/player/vids/'.$arr[1].'.aspx'; //Acfun API
	  $var = '';
	  $json = curl_data($redirect_url,$var);
	  $redirect_url_info = json_decode($json,true);
	  if(!empty($redirect_url_info['vurl'])){
	  	$video_url =  $redirect_url_info['vurl'];
	  	echo '<textarea id="dll2">'.$redirect_url_info['vinfo']['title'].'</textarea>';
	  }
	  //echo $redirect_url_info['cid'];
	  //print_r($redirect_url_info);
      //echo $video_url;
	  //echo $redirect_url_info['vtype'];
	  /*古老的视频的处理办法,由于AC古老视频分为两种，一种是信息很少的，另一种是有信息，但是$redirect_url_info['vurl']只是一串数字，视频信息以后用file_get_contents($url)获取*/
	  if(preg_match("/^[\d]{5,15}$/",@$video_url)&&@$redirect_url_info['vinfo']['title']==''){ 
			$type = array(0=>'',1=>'video.sina');
			$up_id = $redirect_url_info['uid'];
			$up_name = '';
			$up_ava = 'http://w5cdn.ranktv.cn/dotnet/20120923/style/image/avatar.jpg';
			$up_title = '古老视频暂时无法获取标题';
			$up_decription = '古老视频暂时无法获取描述';
			$up_clicks = '0'; 
			$up_stows = '0';
	  }elseif(preg_match("/^[\d]{5,15}$/",@$video_url)&&@$redirect_url_info['vinfo']['title']!=''){ //AC新浪源只返回一串数字 = =||
			$type = array(0=>'',1=>'video.sina');
			$up_id = $redirect_url_info['vinfo']['uid'];
			$up_name = $redirect_url_info['vinfo']['uname'];
			$up_ava = $redirect_url_info['vinfo']['uimg'];
			$up_title = $redirect_url_info['vinfo']['title'];
			$up_decription = $redirect_url_info['vinfo']['description'];
			$up_clicks = $redirect_url_info['vinfo']['clicks']; 
			$up_stows = $redirect_url_info['vinfo']['stows'];
	  }elseif(@$redirect_url_info['vtype']=='qq'){
			$up_id = $redirect_url_info['vinfo']['uid'];
			$up_name = $redirect_url_info['vinfo']['uname'];
			$up_ava = $redirect_url_info['vinfo']['uimg'];
			$up_title = $redirect_url_info['vinfo']['title'];
			$up_decription = $redirect_url_info['vinfo']['description'];
			$up_clicks = $redirect_url_info['vinfo']['clicks']; 
			$up_stows = $redirect_url_info['vinfo']['stows'];
	  }elseif(empty($redirect_url_info['vurl'])){
			
	  }elseif(preg_match('/[a-zA-Z0-9]{12,15}/',$video_url)){  //AC优酷源只返回vid
			$type = array(0=>'',1=>'v.youku');
			$up_id = $redirect_url_info['vinfo']['uid'];
			$up_name = $redirect_url_info['vinfo']['uname'];
			$up_ava = $redirect_url_info['vinfo']['uimg'];
			$up_title = $redirect_url_info['vinfo']['title'];
			@$up_decription = $redirect_url_info['vinfo']['description'];
			$up_clicks = $redirect_url_info['vinfo']['clicks']; 
			$up_stows = $redirect_url_info['vinfo']['stows'];
	  }else{
			/*A站API返回的新浪地址有可能带http也有可能不带http*/
			preg_match('/http:\/\/(.*).com/',$video_url,$type); //判断视频来源
			if(@$type[1]==''){
				$video_url='http://'.$video_url;
				preg_match('/http:\/\/(.*).com/',$video_url,$type);
				$up_id = $redirect_url_info['vinfo']['uid'];
				$up_name = $redirect_url_info['vinfo']['uname'];
				$up_ava = $redirect_url_info['vinfo']['uimg'];
				$up_title = $redirect_url_info['vinfo']['title'];
				$up_decription = $redirect_url_info['vinfo']['description'];
				$up_clicks = $redirect_url_info['vinfo']['clicks']; 
				$up_stows = $redirect_url_info['vinfo']['stows'];
			}else{
				//print_r($type);
				$up_id = $redirect_url_info['vinfo']['uid'];
				$up_name = $redirect_url_info['vinfo']['uname'];
				$up_ava = $redirect_url_info['vinfo']['uimg'];
				$up_title = $redirect_url_info['vinfo']['title'];
				$up_decription = $redirect_url_info['vinfo']['description'];
				$up_clicks = $redirect_url_info['vinfo']['clicks']; 
				$up_stows = $redirect_url_info['vinfo']['stows'];
			}
		}
	}
	  /*判断是否优酷视频、土豆视频，如果是，则使用去15秒广告功能*/
if(isset($type[1])||isset($redirect_url_info['vtype'])){
	 if(@$type[1]!="v.youku"&&@$type[1]!="www.tudou"&&@$type[1]!="video.sina"&&@$redirect_url_info['vtype']!='qq'&&@$type[1]!="you.video.sina"&&$redirect_url_info['vtype']!='pps'){
	 	echo '没找到视频';
	}
	if($redirect_url_info['vtype']=='qq'){
	 $final = extracts($redirect_url_info['vtype'],$video_url);
	 echo '
     <div class="titles" ><a href="'.$url.'" target="_blank" title="点我回到主站对应视频" class="backtoac">'.$up_title.'</a></div>
     <div class="acfun">
         <form name="form" method="post" action="">
            <input type="hidden" value="'.@$prev.'" name="url"/>
            <div id="nav_left" onclick="go_pre();">
                <div class="nav_mark_left">《</div>
            </div>
         </form>
         <object type="application/x-shockwave-flash" data="'.$final.'" id="ac" name="obj1" style="display: block;margin: 0 auto;margin-top: -524px;margin-top: 20px;box-shadow: 0 0 15px black;-webkit-box-shadow: 0 0 15px black;-moz-box-shadow: 0 0 15px black;-o-box-shadow: 0 0 15px black;width: 860px;height: 510px;">
            <param name="quality" value="high">
            <param name="wmode" value="transparent" />
            <param name="allowscriptaccess" value="always">
            <param name="allowfullscreen" value="true">
         </object>
         <form name="form1" method="post" action="">
            <input type="hidden" value="'.@$next.'" name="url"/>
            <div id="nav_right" onclick="go_next();">
                <div class="nav_mark_right">》</div>
            </div>
         </form>
         <a href="http://acfind.sinaapp.com" title="点击视频信息区域可快速返回首页" class="videoinfo"><div class="decription" >介绍:'.$up_decription.'<div id="aai">
			<span class="item">收藏用户：<span class="pts">'.$up_stows.'</span></span>
			<span class="item">播放次数：<span class="pts">'.$up_clicks.'</span></span></div></div>
          </a>
    </div>
    <div class="userinfo"><a href="http://www.acfun.tv/member/user.aspx?uid='.$up_id.'" target="_blank" ><img src="'.$up_ava.'" class="yooo" title="我是UP主'.$up_name.'，少年准备来一发吗？" style="width:100px"></a></div>
    <div class="download" title="功能待添加"></div>
     ';
	$cc=download($url);
		echo '
		<div class="show_dl_menu">
		<span>
			<button class="btn" data-toggle="modal" href="#full-width" id="dl_mv" >下载该视频</button>
			<button class="btn" data-toggle="modal" href="#full-width2" id="dl_dm" >下载弹幕池</button>
			<form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$redirect_url_info['vinfo']['image'].'">
				<button type="submit" name="" class="btn">交封还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$up_ava.'">
				<button type="submit" name="" class="btn">交头还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <a class="btn" href="http://lolimilk.com/acfind.html" target="_blank">更新日志</a>
		</span>
		</div>
		
		<!--弹出下载弹幕层-->
		<div id="full-width" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>'.$up_title.'</h3>
			</div>
			<div class="modal-body" >
				<div class="alert alert-error" style="padding-top: 10px;">推荐使用导出迅雷下载列表下载！列表无法被识别时，请用记事本打开粘贴到迅雷新建任务窗口</div>
				<div  id="show_dl_addr" style="width:680px;"></div>
				<div>
				<button id="get_dl" class="btn btn-large btn-primary">获取下载地址</button>
				<a id="get_lst" class="btn btn-large btn-primary" href="http://acfind-download.stor.sinaapp.com/'.$baseurl.'.lst" target="_blank">导出下载列表</a>
				</div>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭窗口</button>
				<button type="button" class="btn btn-primary" onclick="chgBg()">TX去死</button>
			</div>
		</div>

				<!--弹出下载弹幕层-->
		<div id="full-width2" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>下载弹幕</h3>
			</div>
			<div class="modal-body">
			<div class="alert alert-error" style="padding-top: 10px;">Json格式无需配置，下方配置均为ASS字幕的选项</div>
		<form id="options" method="post" target="_blank" action="http://64.31.18.202:8080/?url='.$url_encode.'">
            <p>
	            <span class="label label-info">字体大小</span>
	            <input class="form-control" type="hidden" name="font_name" value="微软雅黑">
	            <input class="form-control" type="text" name="font_size" value="36">
	            <span class="hint">像素</span></p>
            </p>
            <p>
	            <span class="label label-info">同屏行数</span>
	            <input class="form-control" type="text" name="line_count" value="5">
	            <span class="hint">行</span>
            </p>
            <p>
	            <span class="label label-info">底边距离</span>
	            <input class="form-control" type="text" name="bottom_margin" value="54">
	            <span class="hint">像素</span>
            </p>
            <p>
	            <span class="label label-info">调整秒数</span>
	            <input class="form-control" type="text" name="tune_seconds" value="0">
	            <span class="hint">秒</span>
            </p>
            <p>
	            <span class="label label-info">设分辨率</span>
	            <input class="form-control" type="text" name="video_width" value="1366">
	            x
	            <input class="form-control" type="text" name="video_height" value="768">
	            <span class="hint">像素</span>
            </p>
        <input type="hidden" name="url" value="'.$url.'">
        <input class="btn btn-large btn-primary" style="margin-top: 21px;position: absolute;margin-left:200px;" type="submit" value="下载ASS外挂字幕" target="_balnk">
    </form>
    <a href="http://comment.acfun.tv/'.$redirect_url_info['cid'].'.json" class="btn btn-large btn-primary" title="此格式弹幕与上方参数配置无关">下载Json格式弹幕</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭</button>
				<button type="button" class="btn btn-primary">确定</button>
			</div>
		</div>
		';
	}
	if($redirect_url_info['vtype']=='pps'){
	 $final = extracts($redirect_url_info['vtype'],$video_url);
	 echo '
     <div class="titles" ><a href="'.$url.'" target="_blank" title="点我回到主站对应视频" class="backtoac">'.$up_title.'</a></div>
     <div class="acfun">
         <form name="form" method="post" action="">
            <input type="hidden" value="'.@$prev.'" name="url"/>
            <div id="nav_left" onclick="go_pre();">
                <div class="nav_mark_left">《</div>
            </div>
         </form>
         <object type="application/x-shockwave-flash" data="'.$final.'" id="ac" name="obj1" style="display: block;margin: 0 auto;margin-top: -524px;margin-top: 20px;box-shadow: 0 0 15px black;-webkit-box-shadow: 0 0 15px black;-moz-box-shadow: 0 0 15px black;-o-box-shadow: 0 0 15px black;width: 860px;height: 510px;">
            <param name="quality" value="high">
            <param name="wmode" value="transparent" />
            <param name="allowscriptaccess" value="always">
            <param name="allowfullscreen" value="true">
         </object>
         <form name="form1" method="post" action="">
            <input type="hidden" value="'.@$next.'" name="url"/>
            <div id="nav_right" onclick="go_next();">
                <div class="nav_mark_right">》</div>
            </div>
         </form>
         <a href="http://acfind.sinaapp.com" title="点击视频信息区域可快速返回首页" class="videoinfo"><div class="decription" >介绍:'.$up_decription.'<div id="aai">
            <span class="item">收藏用户：<span class="pts">'.$up_stows.'</span></span>
            <span class="item">播放次数：<span class="pts">'.$up_clicks.'</span></span></div></div>
         </a>
     </div>
     <div class="userinfo"><a href="http://www.acfun.tv/member/user.aspx?uid='.$up_id.'" target="_blank" ><img src="'.$up_ava.'" class="yooo" title="我是UP主'.$up_name.'，少年准备来一发吗？" style="width:100px"></a></div>
     <div class="download" title="功能待添加"></div>
			';
	$cc=download_pps($video_url);
		echo '
		<div class="show_dl_menu">
		<span>
			<button class="btn" data-toggle="modal" href="#full-width" id="dl_mv" >下载该视频</button>
			<button class="btn" data-toggle="modal" href="#full-width2" id="dl_dm" >下载弹幕池</button>
			<form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$redirect_url_info['vinfo']['image'].'">
				<button type="submit" name="" class="btn">交封还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$up_ava.'">
				<button type="submit" name="" class="btn">交头还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <a class="btn" href="http://lolimilk.com/acfind.html" target="_blank">更新日志</a>
		</span>
		</div>

		<!--弹出下载弹幕层-->
		<div id="full-width" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>'.$up_title.'</h3>
			</div>
			<div class="modal-body" >
				<div class="alert alert-error" style="padding-top: 10px;">推荐使用导出迅雷下载列表下载！列表无法被识别时，请用记事本打开粘贴到迅雷新建任务窗口</div>
				<div  id="show_dl_addr"></div>
				<div>
				<button id="get_dl" class="btn btn-large btn-primary">获取下载地址</button>
				<a id="get_lst" class="btn btn-large btn-primary" href="http://acfind-download.stor.sinaapp.com/'.$baseurl.'.lst" target="_blank">导出下载列表</a>
				</div>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭窗口</button>
				<button type="button" class="btn btn-primary" onclick="chgBg()">TX去死</button>
			</div>
		</div>

				<!--弹出下载弹幕层-->
		<div id="full-width2" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>下载弹幕</h3>
			</div>
			<div class="modal-body">
			<div class="alert alert-error" style="padding-top: 10px;">Json格式无需配置，下方配置均为ASS字幕的选项</div>
		<form id="options" method="post" target="_blank" action="http://64.31.18.202:8080/?url='.$url_encode.'">
            <p>
	            <span class="label label-info">字体大小</span>
	            <input class="form-control" type="hidden" name="font_name" value="微软雅黑">
	            <input class="form-control" type="text" name="font_size" value="36">
	            <span class="hint">像素</span></p>
            </p>
            <p>
	            <span class="label label-info">同屏行数</span>
	            <input class="form-control" type="text" name="line_count" value="5">
	            <span class="hint">行</span>
            </p>
            <p>
	            <span class="label label-info">底边距离</span>
	            <input class="form-control" type="text" name="bottom_margin" value="54">
	            <span class="hint">像素</span>
            </p>
            <p>
	            <span class="label label-info">调整秒数</span>
	            <input class="form-control" type="text" name="tune_seconds" value="0">
	            <span class="hint">秒</span>
            </p>
            <p>
	            <span class="label label-info">设分辨率</span>
	            <input class="form-control" type="text" name="video_width" value="1366">
	            x
	            <input class="form-control" type="text" name="video_height" value="768">
	            <span class="hint">像素</span>
            </p>
        <input type="hidden" name="url" value="'.$url.'">
        <input class="btn btn-large btn-primary" style="margin-top: 21px;position: absolute;margin-left:200px;" type="submit" value="下载ASS外挂字幕" target="_balnk">
    </form>
    <a href="http://comment.acfun.tv/'.$redirect_url_info['cid'].'.json" class="btn btn-large btn-primary" title="此格式弹幕与上方参数配置无关">下载Json格式弹幕</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭</button>
				<button type="button" class="btn btn-primary">确定</button>
			</div>
		</div>
		';
	}
	if(@$type[1]=="video.sina"||@$type[1]=='you.video.sina'){
		$final = extracts($type[1],$video_url);
	 echo '
     <div class="titles" ><a href="'.$url.'" target="_blank" title="点我回到主站对应视频" class="backtoac">'.$up_title.'</a></div>
     <div class="acfun">
         <form name="form" method="post" action="">
			<input type="hidden" value="'.$prev.'" name="url"/>
			<div id="nav_left" onclick="go_pre();">
				<div class="nav_mark_left">《</div>
			</div>
		 </form>
         <object type="application/x-shockwave-flash" data="http://you.video.sina.com.cn/api/sinawebApi/outplayrefer.php/vid='.$final.'/s.swf" id="ac" name="obj1" style="display: block;margin: 0 auto;margin-top: -524px;margin-top: 20px;box-shadow: 0 0 15px black;-webkit-box-shadow: 0 0 15px black;-moz-box-shadow: 0 0 15px black;-o-box-shadow: 0 0 15px black;width: 860px;height: 510px;">
            <param name="quality" value="high">
            <param name="wmode" value="transparent" />
            <param name="allowscriptaccess" value="always">
            <param name="allowfullscreen" value="true">
         </object>
         <form name="form1" method="post" action="">
			<input type="hidden" value="'.$next.'" name="url"/>
			<div id="nav_right" onclick="go_next();">
				<div class="nav_mark_right">》</div>
			</div>
		 </form>
         <a href="http://acfind.sinaapp.com" title="点击视频信息区域可快速返回首页" class="videoinfo"><div class="decription" >介绍:'.$up_decription.'<div id="aai">
					<span class="item">收藏用户：<span class="pts">'.$up_stows.'</span></span>
					<span class="item">播放次数：<span class="pts">'.$up_clicks.'</span></span></div></div>
         </a>
    </div>
    <div class="userinfo"><a href="http://www.acfun.tv/member/user.aspx?uid='.$up_id.'" target="_blank" ><img src="'.$up_ava.'" class="yooo" title="我是UP主'.$up_name.'，少年准备来一发吗？" style="width:100px"></a></div>
    <div class="download" title="功能待添加"></div>
         ';
	$cc=download($url);
		echo '
		<div class="show_dl_menu">
		<span>
			<button class="btn" data-toggle="modal" href="#full-width" id="dl_mv" >下载该视频</button>
			<button class="btn" data-toggle="modal" href="#full-width2" id="dl_dm" >下载弹幕池</button>
			<form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$redirect_url_info['vinfo']['image'].'">
				<button type="submit" name="" class="btn" id="gugeniang" >交封还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$up_ava.'">
				<button type="submit" name="" class="btn">交头还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <a class="btn" href="http://lolimilk.com/acfind.html" target="_blank">更新日志</a>
		</span>
		</div>
		
		<!--弹出下载弹幕层-->
		<div id="full-width" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>'.$up_title.'</h3>
			</div>
			<div class="modal-body" >
				<div class="alert alert-error" style="padding-top: 10px;">推荐使用导出迅雷下载列表下载！列表无法被识别时，请用记事本打开粘贴到迅雷新建任务窗口</div>
				<div  id="show_dl_addr" style="width:680px;"></div>
				<div>
				<button id="get_dl" class="btn btn-large btn-primary">获取下载地址</button>
				<a id="get_lst" class="btn btn-large btn-primary" href="http://acfind-download.stor.sinaapp.com/'.$baseurl.'.lst" target="_blank">导出下载列表</a>
				</div>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭窗口</button>
				<button type="button" class="btn btn-primary" onclick="chgBg()">渣浪去死</button>
			</div>
		</div>

				<!--弹出下载弹幕层-->
		<div id="full-width2" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>下载弹幕</h3>
			</div>
			<div class="modal-body">
			<div class="alert alert-error" style="padding-top: 10px;">Json格式无需配置，下方配置均为ASS字幕的选项</div>
		<form id="options" method="post" target="_blank" action="http://64.31.18.202:8080/?url='.$url_encode.'">
            <p>
	            <span class="label label-info">字体大小</span>
	            <input class="form-control" type="hidden" name="font_name" value="微软雅黑">
	            <input class="form-control" type="text" name="font_size" value="36">
	            <span class="hint">像素</span></p>
            </p>
            <p>
	            <span class="label label-info">同屏行数</span>
	            <input class="form-control" type="text" name="line_count" value="5">
	            <span class="hint">行</span>
            </p>
            <p>
	            <span class="label label-info">底边距离</span>
	            <input class="form-control" type="text" name="bottom_margin" value="54">
	            <span class="hint">像素</span>
            </p>
            <p>
	            <span class="label label-info">调整秒数</span>
	            <input class="form-control" type="text" name="tune_seconds" value="0">
	            <span class="hint">秒</span>
            </p>
            <p>
	            <span class="label label-info">设分辨率</span>
	            <input class="form-control" type="text" name="video_width" value="1366">
	            x
	            <input class="form-control" type="text" name="video_height" value="768">
	            <span class="hint">像素</span>
            </p>
        <input type="hidden" name="url" value="'.$url.'">
        <input class="btn btn-large btn-primary" style="margin-top: 21px;position: absolute;margin-left:200px;" type="submit" value="下载ASS外挂字幕" target="_balnk">
    </form>
    <a href="http://comment.acfun.tv/'.$redirect_url_info['cid'].'.json" class="btn btn-large btn-primary" title="此格式弹幕与上方参数配置无关">下载Json格式弹幕</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭</button>
				<button type="button" class="btn btn-primary">确定</button>
			</div>
		</div>
		';
}
	if(@$type[1]=="www.tudou"){
		$final = extracts($type[1],$video_url);	
	 echo '
     <div class="titles" ><a href="'.$url.'" target="_blank" title="点我回到主站对应视频" class="backtoac">'.$up_title.'</a></div>
     <div class="acfun">
     <form name="form" method="post" action="">
		<input type="hidden" value="'.@$prev.'" name="url"/>
		<div id="nav_left" style="height:524px;" onclick="go_pre();">
			<div class="nav_mark_left">《</div>
		</div>
	 </form>
		<iframe id="ac" frameborder=0 style="display: block;margin: 0 auto;margin-top: -524px;margin-top: 20px;padding-left: 90px;left: 20px;height: 524px;width: 860px;margin-left: -90px;" src="http://www.tudou.com/programs/view/html5embed.action?code='.$final.'&quality=high"></iframe>
     <form name="form1" method="post" action="">
		<input type="hidden" value="'.@$next.'" name="url"/>
		<div id="nav_right" style="height:524px;" onclick="go_next();">
			<div class="nav_mark_right">》</div>
		</div>
	 </form>
        <a href="http://acfind.sinaapp.com" title="点击视频信息区域可快速返回首页" class="videoinfo"><div class="decription" >介绍:'.$up_decription.'<div id="aai">
			<span class="item">收藏用户：<span class="pts">'.$up_stows.'</span></span>
			<span class="item">播放次数：<span class="pts">'.$up_clicks.'</span></span></div></div>
        </a>
     </div>
     <div class="userinfo"><a href="http://www.acfun.tv/member/user.aspx?uid='.$up_id.'" target="_blank" ><img src="'.$up_ava.'" class="yooo" title="我是UP主'.$up_name.'，少年准备来一发吗？" style="width:100px"></a></div>
     <div class="download" title="功能待添加"></div>
		';
	$cc=download_tudou($url);
			echo '
		<div class="show_dl_menu">
		<span>
			<button class="btn" data-toggle="modal" href="#full-width" id="dl_mv" >下载该视频</button>
			<button class="btn" data-toggle="modal" href="#full-width2" id="dl_dm" >下载弹幕池</button>
			<form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$redirect_url_info['vinfo']['image'].'">
				<button type="submit" name="" class="btn">交封还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$up_ava.'">
				<button type="submit" name="" class="btn">交头还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <a class="btn" href="http://lolimilk.com/acfind.html" target="_blank">更新日志</a>
		</span>
		</div>

		<!--弹出下载弹幕层-->
		<div id="full-width" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>'.$up_title.'</h3>
			</div>
			<div class="modal-body" >
				<div class="alert alert-error" style="padding-top: 10px;">推荐使用导出迅雷下载列表下载！列表无法被识别时，请用记事本打开粘贴到迅雷新建任务窗口</div>
				<div  id="show_dl_addr"></div>
				<button id="get_dl" class="btn btn-large btn-primary">获取下载地址</button>
				<a id="get_lst" class="btn btn-large btn-primary" href="http://acfind-download.stor.sinaapp.com/'.$baseurl.'.lst" target="_blank">导出下载列表</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭窗口</button>
				<button type="button" class="btn btn-primary" onclick="chgBg()">土豆去死</button>
			</div>
		</div>

				<!--弹出下载弹幕层-->
		<div id="full-width2" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>下载弹幕</h3>
			</div>
			<div class="modal-body">
			<div class="alert alert-error" style="padding-top: 10px;">Json格式无需配置，下方配置均为ASS字幕的选项</div>
		<form id="options" method="post" target="_blank" action="http://64.31.18.202:8080/?url='.$url_encode.'">
            <p>
	            <span class="label label-info">字体大小</span>
	            <input class="form-control" type="hidden" name="font_name" value="微软雅黑">
	            <input class="form-control" type="text" name="font_size" value="36">
	            <span class="hint">像素</span></p>
            </p>
            <p>
	            <span class="label label-info">同屏行数</span>
	            <input class="form-control" type="text" name="line_count" value="5">
	            <span class="hint">行</span>
            </p>
            <p>
	            <span class="label label-info">底边距离</span>
	            <input class="form-control" type="text" name="bottom_margin" value="54">
	            <span class="hint">像素</span>
            </p>
            <p>
	            <span class="label label-info">调整秒数</span>
	            <input class="form-control" type="text" name="tune_seconds" value="0">
	            <span class="hint">秒</span>
            </p>
            <p>
	            <span class="label label-info">设分辨率</span>
	            <input class="form-control" type="text" name="video_width" value="1366">
	            x
	            <input class="form-control" type="text" name="video_height" value="768">
	            <span class="hint">像素</span>
            </p>
        <input type="hidden" name="url" value="'.$url.'">
        <input class="btn btn-large btn-primary" style="margin-top: 21px;position: absolute;margin-left:200px;" type="submit" value="下载ASS外挂字幕" target="_balnk">
    </form>
    <a href="http://comment.acfun.tv/'.$redirect_url_info['cid'].'.json" class="btn btn-large btn-primary" title="此格式弹幕与上方参数配置无关">下载Json格式弹幕</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭</button>
				<button type="button" class="btn btn-primary">确定</button>
			</div>
		</div>
';
	}
if(@$type[1]=="v.youku"){
		$final = extracts($type[1],$video_url);
		echo '
        	  
             ';
	 echo '
     <div class="titles"><a href="'.$url.'" target="_blank" title="点我回到主站对应视频" class="backtoac">'.$up_title.'</a></div>
     <div class="acfun">
     	<form name="form" method="post" action="">
			<input type="hidden" value="'.@$prev.'" name="url"/>
			<div id="nav_left" style="height:497px;" onclick="go_pre();">
				<div class="nav_mark_left">《</div>
			</div>
		</form>
     <object type="application/x-shockwave-flash" data="http://static.youku.com/v1.0.0362/v/swf/loader.swf?VideoIDS='.$final.'&winType=adshow&isAutoPlay=true&quality=high" id="ac" name="obj1" style="display: block;margin: 0 auto;margin-top: -524px;margin-top: 20px;box-shadow: 0 0 15px black;-webkit-box-shadow: 0 0 15px black;-moz-box-shadow: 0 0 15px black;-o-box-shadow: 0 0 15px black;width: 860px;height: 498px" >
     	<param name="quality" value="high">
        <param name="wmode" value="transparent" />
        <param name="allowscriptaccess" value="always">
        <param name="allowfullscreen" value="true">
     </object>
     	<form name="form1" method="post" action="">
			<input type="hidden" value="'.@$next.'" name="url"/>
			<div id="nav_right" style="height:497px;" onclick="go_next();">
				<div class="nav_mark_right">》</div>
			</div>
		</form>
        <a href="http://acfind.sinaapp.com" title="点击视频信息区域可快速返回首页" class="videoinfo"><div class="decription" >介绍:'.$up_decription.'<div id="aai">
			<span class="item">收藏用户：<span class="pts">'.$up_stows.'</span></span>
			<span class="item">播放次数：<span class="pts">'.$up_clicks.'</span></span></div></div>
        </a>
     </div>
     <div class="userinfo"><a href="http://www.acfun.tv/member/user.aspx?uid='.$up_id.'" target="_blank" ><img src="'.$up_ava.'" class="yooo" title="我是UP主'.$up_name.'，少年准备来一发吗？" style="width:100px"></a></div>
     <div class="download" title="功能待添加"></div>
		 ';
	$cc=download_youku($video_url);
		echo '
		<div class="show_dl_menu">
		<span>
			<button class="btn" data-toggle="modal" href="#full-width" id="dl_mv">下载该视频</button>
			<button class="btn" data-toggle="modal" href="#full-width2" id="dl_dm">下载弹幕池</button>
			<form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$redirect_url_info['vinfo']['image'].'">
				<button type="submit" name="" class="btn">交封还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <form method="GET"   action="http://images.google.com.hk/searchbyimage" style="display:inline;" target="_blank">
				<input  type="hidden" name="image_url"  value="'.@$up_ava.'">
				<button type="submit" name="" class="btn">交头还友</button>
				<input type="hidden" name="newwindow" value="1">
			</form>
            <a class="btn" href="http://lolimilk.com/acfind.html" target="_blank">更新日志</a>
		</span>
		</div>
		
		<!--弹出下载弹幕层-->
		<div id="full-width" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>'.$up_title.'</h3>
			</div>
			<div class="modal-body" >
				<div class="alert alert-error" style="padding-top: 10px;">推荐使用导出迅雷下载列表下载！列表无法被识别时，请用记事本打开粘贴到迅雷新建任务窗口</div>
				<div  id="show_dl_addr"></div>
				<button id="get_dl" class="btn btn-large btn-primary">获取下载地址</button>
				<a id="get_lst" class="btn btn-large btn-primary" href="http://acfind-download.stor.sinaapp.com/'.$baseurl.'.lst" target="_blank">导出下载列表</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭窗口</button>
				<button type="button" class="btn btn-primary" onclick="chgBg()">优酷去死</button>
			</div>
		</div>

		<!--弹出下载弹幕层-->
		<div id="full-width2" class="modal container hide fade" tabindex="-1">
			<div class="modal-header">
				 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3>下载弹幕</h3>
			</div>
			<div class="modal-body">
			<div class="alert alert-error" style="padding-top: 10px;">Json格式无需配置，下方配置均为ASS字幕的选项</div>
		<form id="options" method="post" target="_blank" action="http://64.31.18.202:8080/?url='.$url_encode.'">
            <p>
	            <span class="label label-info">字体大小</span>
	            <input class="form-control" type="hidden" name="font_name" value="微软雅黑">
	            <input class="form-control" type="text" name="font_size" value="36">
	            <span class="hint">像素</span></p>
            </p>
            <p>
	            <span class="label label-info">同屏行数</span>
	            <input class="form-control" type="text" name="line_count" value="5">
	            <span class="hint">行</span>
            </p>
            <p>
	            <span class="label label-info">底边距离</span>
	            <input class="form-control" type="text" name="bottom_margin" value="54">
	            <span class="hint">像素</span>
            </p>
            <p>
	            <span class="label label-info">调整秒数</span>
	            <input class="form-control" type="text" name="tune_seconds" value="0">
	            <span class="hint">秒</span>
            </p>
            <p>
	            <span class="label label-info">设分辨率</span>
	            <input class="form-control" type="text" name="video_width" value="1366">
	            x
	            <input class="form-control" type="text" name="video_height" value="768">
	            <span class="hint">像素</span>
            </p>
        <input type="hidden" name="url" value="'.$url.'">
        <input class="btn btn-large btn-primary" style="margin-top: 21px;position: absolute;margin-left:200px;" type="submit" value="下载ASS外挂字幕" target="_balnk">
    </form>
    <a href="http://comment.acfun.tv/'.$redirect_url_info['cid'].'.json" class="btn btn-large btn-primary" title="此格式弹幕与上方参数配置无关">下载Json格式弹幕</a>
			</div>
			 <div class="modal-footer">
				<button type="button" data-dismiss="modal" class="btn">关闭</button>
				<button type="button" class="btn btn-primary">确定</button>
			</div>
		</div>
		';
	}
}else{
    echo '<script>alert("无法解析视频!可能是主站视频不存在，或者是史前巨坟!但你还可以使用AC播放器播放！");</script>
    	<input type="button" value="返回首页" class="confirm" style="width: 600px;height: 200px;margin-top: 80px;font-size: 80px;" onclick="window.location.href=\'http://acfind.sinaapp.com\'">
        <form name="form1" method="post" action="http://acfind.sinaapp.com/index.php">
			<input type="hidden" value="'.@$url.'" name="errorurl"/>
			<input type="button" value="AC播放器播放" class="confirm" style="width: 600px;height: 200px;margin-top: 80px;font-size: 80px;" onclick="go_next();">
		</form>
        ';
    	$song = 'song'.rand(1,4).'.mp3';
		echo '<embed src="'.$song.'" hidden="true" border="0" autostart="true" loop="true">';
	  }
}
}else{
	echo '
	<div id="stage">
	<div class="head" title="点我刷新页面" ><a href="javascript:document.location.reload();"><img src="image/logo.png" class="logo"></a></div>
	<div class="form">
			<form action="index.php" method="post">
				<input type="text" onmouseover="this.select()" name="url" class="txts" title="输入A站视频地址,支持完整地址或者ac号，完成后点击一发入魂！" placeholder="请在此输入喵~"/>'.'<br><br>'.'
				<button type="submit" class="confirm" />一发♂入魂</button>
			</form>
	</div>
	</div>';
	}
?>
<div id='cse' style='top: 0;color: #FF5200; text-align:center; '>正在载入AC专用谷歌娘搜索器…</div>
<script src='//www.google.com/jsapi' type='text/javascript'></script>
<script type='text/javascript'>
	google.load('search', '1', {language: 'zh-Hans', style: google.loader.themes.V2_DEFAULT}); 
	google.setOnLoadCallback(function() {
  	var customSearchOptions = {};
  	var orderByOptions = {};
  	orderByOptions['keys'] = [{label: 'Relevance', key: ''} , {label: 'Date', key: 'date'}];
  	customSearchOptions['enableOrderBy'] = true;
  	customSearchOptions['orderByOptions'] = orderByOptions;
  	customSearchOptions['overlayResults'] = true;
  	var customSearchControl =   new google.search.CustomSearchControl('011185792368681800309:ynktq1v4z6g', customSearchOptions);
  	customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
  	var options = new google.search.DrawOptions();
  	options.setAutoComplete(true);
  	customSearchControl.draw('cse', options);
}, true);
</script>
<link type="text/css" rel="stylesheet" href="css/search.css" />
	<script type="text/javascript">
		$(function() {
			$('.txts').powerTip({placement: 'n'});
			$('.head').powerTip({placement: 's'});
			$('.footer').powerTip({placement: 'n'});
			$('#duniang').powerTip({placement: 'e'});
			$('#gugeniang').powerTip({placement: 'n'});
			$('#dl_dm').powerTip({placement: 'n'});
			$('#dl_mv').powerTip({placement: 'n'});
			$('#mv_body').powerTip({followMouse: true});
			$('.videoinfo').powerTip({followMouse: true});
			$('.download').powerTip({followMouse: true});
			$('.yooo').powerTip({followMouse: true});
			$('.backtoac').powerTip({followMouse: true});
			/*$('.decription').data('powertipjq', $([
				'<p><b>点击视屏信息区域可快速返回首页</b></p>'
			].join('\n')));
			$('.decription').powerTip({
				placement: 'w',
				mouseOnToPopup: true
			});*/
			
			// api 
			$('#open').on('click', function() {
				$.powerTip.showTip($('#some'));
			});
			$('#close').on('click', function() {
				$.powerTip.closeTip();
			});
			});
		$("#get_dl").click(function(){
			$("html,body").animate({scrollTop:$("#show_dl_addr").offset().top},1000);
		});
		$("#move_away").click(function(){
			$(".acfun").css("visibility","hidden");
		});
        $("#move_in").click(function(){
			$(".acfun").css("visibility","visible");
		});
	</script>

	<script type="text/javascript">
		var oBtn=document.getElementById("get_dl");
		var oT=document.getElementsByClassName("dll");
		var oV=document.getElementById("show_dl_addr");
		var c='';
		oBtn.onclick=function(){
			for(i=0;i<oT.length;i++){
				c=c+oT[i].value;
			}
		oV.innerHTML=c+'<br>';
		}
	</script>

	<div id="footer"> Copyright 2013 By 橙橙@<a href="http://lolimilk.com" title="我的个人博客" class="footer" target="_balnk">lolimilk</a> Email:admin#lolimilk.com
    	<a href="http://info.flagcounter.com/xBye"><img src="http://s05.flagcounter.com/mini/xBye/bg_FFFFFF/txt_000000/border_CCCCCC/flags_0/" alt="Flag Counter" border="0"></a>
    </div>

</body>
</html>