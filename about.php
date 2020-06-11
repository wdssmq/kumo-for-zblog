<!--
  <div class="SubMenu">
    <a href="main.php" title="首页"><span class="m-left m-now">首页</span></a>
    <?php require_once "about.php"; ?>
  </div>
-->

<style type="text/css">
  #divMain .SubMenu {
    height: auto;
  }

  i.xnxf-tip {
    background-color: red;
    border-radius: 7px;
    height: 7px;
    padding: 0;
    position: relative;
    right: 8px;
    top: -3px;
    width: 7px;
  }

  #help {
    padding-top: 1em;
  }

  #help a {
    background-color: inherit;
    float: none;
  }

  hr {
    background-color: navajowhite;
    border: none;
    display: block;
    height: 1px;
    margin: 1rem 0;
    visibility: visible;
  }
</style>
<i class="xnxf-tip" style="float: right"></i>
<a href="javascript:;" style="float: right;" onclick="$('#help,#divMain2').slideToggle();$('.xnxf-tip').animate({opacity: 0});SetCookie('xnxf-tip', '2020-06-03', 365)" title="查看/隐藏关于"><span class="m-right">[关于]</span></a>
<div id="help" style="display:none;clear: both">
  <p>QQ：349467624 <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&amp;uin=349467624&amp;site=qq&amp;menu=yes" title="沉冰浮水的QQ"><img src="https://www.wdssmq.com/zb_users/logos/qq_button.png" alt="QQ" title="QQ"></a></p>
  <p>QQ群：189574683 <a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=7a69c73adf802624837fcad9e9294a82263b94269408930501b34247ee2cbd2c" title="我的咸鱼心"><img src="//pub.idqqimg.com/wpa/images/group.png" alt="我的咸鱼心" title="我的咸鱼心"></a></p>
  <p>博客：<a target="_blank" href="https://www.wdssmq.com" title="沉冰浮水">https://www.wdssmq.com</a></p>
  <p>Feed：<a target="_blank" href="http://feed.wdssmq.com" title="Feed">http://feed.wdssmq.com</a></p>
  <p>bilibili：<a target="_blank" href="https://space.bilibili.com/44744006" title="bilibili">https://space.bilibili.com/44744006</a></p>
  <p>知乎：<a target="_blank" href="https://www.zhihu.com/people/wdssmq" title="知乎">https://www.zhihu.com/people/wdssmq</a></p>
  <p>GitHub：<a target="_blank" href="https://github.com/wdssmq" title="GitHub">https://github.com/wdssmq</a></p>
  <p>GreasyFork：<a target="_blank" href="https://greasyfork.org/zh-CN/users/6865-wdssmq" title="GreasyFork">https://greasyfork.org/zh-CN/users/6865-wdssmq</a></p>
  <hr>
  <p>爱发电：<a target="_blank" href="https://afdian.net/@wdssmq" title="爱发电">https://afdian.net/@wdssmq</a></p>
  <p>[AD：<a href="http://www.webweb.com/index.asp?upline=wdssmq" target="_blank" title="香港主机" rel="nofollow">ASP香港主机</a> 优惠码： WXXX10%OFF ]</p>
  <p>[AD：<a title="老薛主机" target="_blank" href="https://my.laoxuehost.net/aff.php?aff=294">PHP美国空间</a> 优惠码：15off-xnxf ]</p>
  <p>[AD：<a title="主机云" target="_blank" href="https://my.hostyun.com/page.aspx?c=referral&u=8680">主机云</a>]</p>
  <p>[AD：<a title="Vultr" target="_blank" href="https://www.vultr.com/?ref=7663955">Vultr</a>]</p>
</div>
<div style="clear: both"></div>
<script type="text/javascript">
  if (GetCookie("xnxf-tip") == '2020-06-03') {
    $('.xnxf-tip').css({
      opacity: 0
    });
  }
</script>
