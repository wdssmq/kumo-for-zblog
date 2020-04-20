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
  </div>
  <div id="divMain2">
     <form action="<?php echo BuildSafeURL("main.php?act=save");?>" method="post">
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
       <td>点击保存→</td>
       <td colspan="2"><input type="submit" value="提交" /><small><a title="刷新" href="main.php" style="font-size: 16px;display: inline-block;margin-left: 5px;">刷新</a></small></td>
       </tr>
       </table>
     </form>
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
