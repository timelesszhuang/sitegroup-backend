<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><meta name="keywords" content="" />

<head>
<meta name="description" content="云协作" />
<meta name="keywords" content="有道云协作">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>有道云协作-首页</title>
<script src="js/jquery-1.4.2.min.js"></script>

<!--<script src="jquery-1.9.1.js"></script>-->
<script src="js/slider.js"></script>    

    <link rel="stylesheet" href="css/contact.css">
</head>
<?php include("common.php");?>
<script>
    $(function(){
        $('#submit').click(function () {
            var data = $('#theform').serialize();
            var url = 'http://salesman.cc/index.php/Shuaidan_ceshi/PublicTry/index';
            var name=$("#userNameSales").val();
            var phone=$("#userPhoneSales").val();
            var email=$("#userEmail").val();
            var company=$("#userCompany").val();
            $("#theform")[0].reset();
            $.ajax({
                type: "POST",
                dataType: "json",
                //因为如果是异步执行的话  没有返回就执行return 了 但是同步的话还是会有问题的 比如长时间没有相应的话会阻塞
                async: true,
                url: url,
                data: data,
                success: function (data) {
                    if(data.status==20){
                        $("#userNameSales").val(name);
                        $("#userPhoneSales").val(phone);
                        $("#userEmail").val(email);
                        $("#userCompany").val(company);
                    }
                    alert(data.msg);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('尊敬的用户，我们已经收到您的请求，稍后会有专属客服为您服务。');
                }
            });
        });
    //----------------
        $('#submit2').click(function () {
            var data = $('#theform2').serialize();
            var url = 'http://salesman.cc/index.php/Shuaidan_ceshi/PublicTry/index';
            var name=$("#userNameSales2").val();
            var phone=$("#userPhoneSales2").val();
            var email=$("#userEmail2").val();
            var company=$("#userCompany2").val();
            $("#theform2")[0].reset();
            $.ajax({
                type: "POST",
                dataType: "json",
                //因为如果是异步执行的话  没有返回就执行return 了 但是同步的话还是会有问题的 比如长时间没有相应的话会阻塞
                async: true,
                url: url,
                data: data,
                success: function (data) {
                    if(data.status==20){
                        $("#userNameSales2").val(name);
                        $("#userPhoneSales2").val(phone);
                        $("#userEmail2").val(email);
                        $("#userCompany2").val(company);
                    }
                    alert(data.msg);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert('尊敬的用户，我们已经收到您的请求，稍后会有专属客服为您服务。');
                }
            });
        });

    });

</script>
<script type="text/javascript">
$(function(){

	$('#demo01').flexslider({
		animation: "slide",
		direction:"horizontal",
		easing:"swing"
	});
});
</script>
<body>
<div class="sidebar">
    <div class="sidebar_icon back">
    	<a href="#top"><img src="images/icon-11.png">
    </div>
</div>
<a name="top"></a>



<div class="header_box">
<div class="header">
	<div class="logo_box">
    	<img src="images/img-logo1.png">
        
    </div>
    <ul class="nav">
        <li><a href="index.php">首页</a></li>
        <li><a href="index.php">云端存储</a></li>
        <li><a href="index.php">项目管理</a></li>
        <li><a href="index.php">协同编辑</a></li>
        <li><a href="lxwm.php">联系我们</a></li>
        <div class="clearfix"></div>
    </ul>
    <div class="zhuce">注册</div>
    <div class="tel"><b>咨询电话：4000-086-163</b></div>
    <div class="clearfix"></div>
</div>
</div>

<script>
$(".nav a:eq(4)").addClass("contact")
$(".nav a").click(
	function(){
		$(".nav a").removeClass("contact")
		$(this).addClass("contact")
	}
)

</script>



	<div id="demo01" class="flexslider">
	<ul class="slides">
		<li><div class="img" style="background:url(images/img-banner.jpg) no-repeat;background-position:center center; height:450px;" ></div></li>
        <li><div class="img" style="background: url(images/banner02.png) no-repeat;background-position:center center; height:450px;" ></div></li>
	</ul>

    <div class="shenqing_box">
    	<h1>马上试用&nbsp;&nbsp;提高效率</h1>
        <form class="shenqing" id="theform">
            <div>
                <label>姓名：</label>
                <input type="text" name="name" id="userNameSales" placeholder="请输入联系人姓名">
            </div>
            <div>
                <label>电话：</label>
                <input type="text" name="phone" id="userPhoneSales" placeholder="电话号码/手机号码">
            </div>
            <div>
                <label>账号：</label>
                <input type="text" name="email" id="userEmail" placeholder="请输入邮箱地址">
            </div>
            <div>
                <label>公司：</label>
                <input type="text" name="company" id="userCompany" placeholder="请输入公司名称">
            </div>
            <!--隐藏表单-->
            <input type="hidden" name="shuaidan_type" value="2">
            <input type="hidden"  value="<?php echo $ip; ?>" name="ip"/>
            <input type="hidden"  value="<?php echo $query_string; ?>" name="query_string"/>
            <!--这个参数是从搜索引擎中来-->
            <input type="hidden" value="<?php echo $key_word; ?>" name="key_word"/>
            <!--搜索引擎-->
            <input type="hidden" value="<?php echo $search_engine; ?>" name="search_engine"/>
            <!--搜索引擎传递过来的地域信息-->
            <input type="hidden" value="<?php echo $s_val; ?>" name="s_val"/>
            <!--位置信息 比如是qiangbi  还是胜途的区分-->
            <input type="hidden" value="<?php echo $pos; ?>" name="pos"/>
            <!--表示是谁的客户 表示salesmen 中的 职员的id-->
            <input type="hidden" value="<?php echo $s?>" name="s">
        </form>
        <input class="btn" type="submit" value="提交申请" id="submit">
    </div>
</div>
<script>
var img=$(".move img").length//图的个数
var w = img*1920//图片盒子宽度
$(".move").width(w)
var n=0
function moveLeft(){
	if(n<img-1){
		n=n+1//向左移动
	}else{
		n=1//从0播放1
		$(".move").css("marginLeft",-960)//回0所在的位置
	}
	$(".move").animate({marginLeft:-960-1920*n})	
	if(n==img-1){//如果是最后一张（假第1张）圆点显示当前项为第一个按钮
		$(".banner_box li").eq(0).addClass("current").siblings().removeClass("current")
	}
	$(".banner_box li").eq(n).addClass("current").siblings().removeClass("current")
}
setInterval(moveLeft,2500)


	
</script>


<div class="lxwm">
	<div class="about">
		<h1>联系我们</h1>
    
    	<p>联系信息：北京易至信科技有限公司 （总部）<br />
    	  地　址：北京市海淀区信息号1号上地国际创业园西区1号楼901室<br />
    	  电　话：010-85170726 <br />
    	  <span>400免费电话:400-0086-163</span><br />
    	  山东分公司<br />
    	  地址：山东省济南市山大路47号数码港大厦C-807<br />
    	  电话：0531-88554123，67899163<br />
    	  <span>400免费电话 :400-0086-163</span><br />
    	  郑州分公司<br />
    	  地　址：郑州市金水区经三路农业路交叉口财富广场2号楼1104<br />
    	  电　话：0371-53377163 <br />
   	      <span>400免费电话 : 4000-460-365</span></p>
	</div></div>



<div class="footer_box">
<div class="footer"><!--<meta name="keywords" content="" />
 -->
	<div class="footer_left">
        <img src="images/footer_logo.png">
        <div class="erweima"><img src="images/erweima.png"></div>
    </div>
    
	<div class="footer_center">    
        <ul>
            <li><a href="">有道云协作</a></li>
            <li><a href="http://mail.qiangbi.net/" target="_blank">企业邮箱</a></li>
            <li><a href="lianxi.html" class="no_border">联系我们</a></li>
            <div class="clearfix"></div>
        </ul>
           
        <p>有道云协作<br>企业文件管理协同专业平台！</p>
        <div class="pingtai">
        	<a href="http://note.youdao.com/download.html#win" target="_blank">
            <img src="images/img-windows.png"></a>
            <a href="http://note.youdao.com/download.html#android" target="_blank">
            <img src="images/img-android.png"></a>
            <a href="http://note.youdao.com/download.html#iphone" target="_blank">
            <img src="images/img-iphone.png"></a>
            <div class="clearfix"></div>
        </div>
        
     </div>
     
     <dl class="footer_right">
     	<dt>商务合作</dt>
        <dd>Email:kf@qiangbi.net</dd>
        <dd>电话：010-85170726&nbsp;&nbsp;4000-086-163</dd>
        <dd>地址：北京市海淀区信息号上地国际<br>创业园西区1号楼901室</dd>
     </dl>
     <span><img src="images/banquan.png"></span>
</div>
</div>

<div class="big_box">
<div class="zhucetanchu_box">
    	<h1>我们将尽快为您开通免费试用</h1>
        <div class="chahao">×</div>
        <form class="zhucetanchu" id="theform2">
            <div>
                <label>姓名：</label>
                <input type="text" name="name" id="userNameSales2"  placeholder="请输入联系人姓名">
            </div>
            <div>
                <label>电话：</label>
                <input type="text" name="phone"  id="userPhoneSales2" placeholder="电话号码/手机号码">
            </div>
            <div>
                <label>账号：</label>
                <input type="text" name="email" id="userEmail2" placeholder="请输入邮箱地址">
            </div>
            <div>
                <label>公司：</label>
                <input type="text" name="company" id="userCompany2" placeholder="请输入公司名称">
            </div>
            <!--隐藏表单-->
            <input type="hidden" name="shuaidan_type" value="2">
            <input type="hidden"  value="<?php echo $ip; ?>" name="ip"/>
            <input type="hidden"  value="<?php echo $query_string; ?>" name="query_string"/>
            <!--这个参数是从搜索引擎中来-->
            <input type="hidden" value="<?php echo $key_word; ?>" name="key_word"/>
            <!--搜索引擎-->
            <input type="hidden" value="<?php echo $search_engine; ?>" name="search_engine"/>
            <!--搜索引擎传递过来的地域信息-->
            <input type="hidden" value="<?php echo $s_val; ?>" name="s_val"/>
            <!--位置信息 比如是qiangbi  还是胜途的区分-->
            <input type="hidden" value="<?php echo $pos; ?>" name="pos"/>
            <!--表示是谁的客户 表示salesmen 中的 职员的id-->
            <input type="hidden" value="<?php echo $s?>" name="s">
        </form>
        <input class="btn" type="submit" value="提交申请" id="submit2">
    </div>
</div>
<script>
$(".big_box").hide()
$(".zhuce").click(
	function(){
		$(".big_box").show()
		$(".zhuce").addClass("current")
	}
)

$(".chahao").click(
	function(){
		$(".big_box").hide()
		$(".zhuce").removeClass("current")
	}
)
$(".free").click(
	function(){
		$(".big_box").show()
		
	}
)

$(".chahao").click(
	function(){
		$(".big_box").hide()
	}
)

</script>
</body>
</html>
