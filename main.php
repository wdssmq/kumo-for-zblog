<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
  $zbp->ShowError(6);
  die();
}
if (!$zbp->CheckPlugin('kumo')) {
  $zbp->ShowError(48);
  die();
}
$act = GetVars('act', 'GET');
$suc = GetVars('suc', 'GET');
if (GetVars('act', 'GET') == 'save') {
  CheckIsRefererValid();
  foreach ($_POST as $key => $val) {
    $zbp->Config('kumo')->$key = trim($val);
  }
  $zbp->SaveConfig('kumo');
  $zbp->BuildTemplate();
  $zbp->SetHint('good');
  Redirect('./main.php' . ($suc == null ? '' : '?act=$suc'));
}
$blogtitle = 'kumo';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?><small><a title="刷新" href="main.php" style="font-size: 16px;display: inline-block;margin-left: 5px;">刷新</a></small></div>
  <div class="SubMenu">
    <a href="main.php" title="首页"><span class="m-left m-now">首页</span></a>
    <?php require_once "about.php"; ?>
  </div>
  <div id="divMain2">
    <form action="<?php echo BuildSafeURL("main.php?act=save"); ?>" method="post">
      <table width="100%" class="tableBorder">
        <tr>
          <th width="10%">项目</th>
          <th>内容</th>
          <th width="45%">说明</th>
        </tr>
        <tr>
          <td>调试模式</td>
          <td><?php echo zbpform::zbradio("debug", $zbp->Config("kumo")->debug); ?></td>
          <td>启用调试模式</td>
        </tr>
        <tr>
          <td>规则列表</td>
          <td colspan="2"><?php echo kumo_pjectList(); ?></td>
        </tr>
        <tr>
          <td>点击保存→</td>
          <td colspan="2"><input type="submit" value="提交" /><small><a title="刷新" href="main.php" style="font-size: 16px;display: inline-block;margin-left: 5px;">刷新</a></small></td>
        </tr>
      </table>
    </form>
    <!-- ---- -->
    <p><?php echo kumo_a(kumo_Path("run", "host"), "运行地址", 0, 1); ?></p>
    <p>将usr_sample中的示例规则复制进usr中，然后访问如上地址即可触发；实际使用时可将该地址添加到云监控服务中。</p>
  </div>
  ------
  <h3>赞赏</h3>
  <p class="js-qr">
    <img width="256" src="img/qr-qq.png" alt="QQ" title="QQ">
    <img width="256" src="img/qr-wx.png" alt="微信" title="微信">
    <img width="256" src="img/qr-ali.png" alt="支付宝" title="支付宝">
  </p>
  <br>
</div>

<?php
function kumo_a($href, $title, $text = "", $newWindow = 0)
{
  if (empty($text)) {
    $text = $href;
  }
  $target = $newWindow ? "target=\"_blank\"" : "";
  return "<a {$target} href=\"{$href}\" title=\"{$title}\">$text</a>";
}
function kumo_pjectList()
{
  $arrJSON = array();
  kumo_Initialization($arrJSON);
  $rlt = "";
  foreach ($arrJSON as $name => $path) {
    $project = kumo_ReadJSON($name);
    if (isset($project->title)) {
      $name = "{$project->title}【{$project->name}】";
    }
    // $rlt .= $name;
    $rlt .= kumo_a(kumo_Path("run", "host") . "?name={$project->name}", "运行地址", $name, 1);
    $rlt .= "<br>";
  }
  return $rlt;
}
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
