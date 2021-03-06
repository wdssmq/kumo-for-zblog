<?php
#注册插件
RegisterPlugin("kumo", "ActivePlugin_kumo");

function ActivePlugin_kumo()
{
}
function kumo_Path($file, $t = 'path')
{
  global $zbp;
  $result = $zbp->$t . 'zb_users/plugin/kumo/';
  switch ($file) {
    case "cache":
      $subCache = date("Ymd", time() - time() % (86400 * 13));
      return $result . "cache/{$subCache}/";
    case "img":
      // return $result . "img/";
      return $zbp->$t . "zb_users/upload/";
      break;
    case 'proj':
    case 'usr':
      return $result . 'usr/';
      break;
    case 'run':
      return $result . 'run.php';
      break;
    case 'main':
      return $result . 'main.php';
      break;
    default:
      return $result . $file;
  }
};
function InstallPlugin_kumo()
{
  global $zbp;
  if (!$zbp->HasConfig('kumo')) {
    $zbp->Config('kumo')->version = 1;
    $zbp->Config('kumo')->debug = 1;
    $zbp->SaveConfig('kumo');
  }
  $dir = kumo_Path("usr");
  if (!is_dir($dir)) {
    @mkdir($dir, 0755);
    #code
  }
  // composer require jaeger/querylist
  // require __DIR__ . '/vendor/autoload.php';
  // require __DIR__ . '/class/core.php';
  require __DIR__ . '/class/db.php';
  require __DIR__ . '/class/json.php';
  require __DIR__ . '/function.php';
  kumo_Initialization();
  kumo_Risuto::CreateTable();
}
function UninstallPlugin_kumo()
{
}
