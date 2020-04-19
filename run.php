<?php
require '../../../zb_system/function/c_system_base.php';
$zbp->Load();
ZBlogException::$islogerror = false;

$arrJSON = array();
kumo_Initialization($arrJSON);

$projectName = GetVars("name", "GET");
// $projectName = "test";

if (empty($projectName)) {
  $projectName = array_rand($arrJSON);
}

echo "开始", "<br><br>\n";
echo "加载任务描述文件", "<br><br>\n";
echo "指定入口任务", "<br><br>\n";
echo "执行并将结果入库", "<br><br>\n";

$project = kumo_ReadJSON($projectName);
$task = $project->Get("index");
kumo_Run($task);
echo "-----", "<br><br>\n";
echo "从数据库取出未执行的任务并执行", "<br><br>\n";
echo "过程中会产生新任务并入库", "<br><br>\n";
echo "最终采集结果则组织为文章发布", "<br><br>\n";
$opt = array("num" => 37, "project" => $projectName);
$Risutos = kumo_GetRisuto($opt);
foreach ($Risutos as $Risuto) {
  if ($Risuto->With == "") {
    $Risuto->Del();
    return;
  }
  $task = $project->Get($Risuto->With);
  kumo_Run($task, $Risuto->Url);
  $Risuto->Done = true;
  $Risuto->Save();
}

function kumo_Run($task, $u = "")
{
  global $project;
  $obj = new kumoCore($task, $u);
  // $obj->debug(1);
  if ($obj->ErrCode > 0) {
    $obj->debug(1);
    return $obj->ErrInfo;
  }
  echo "-----<br><br>\n\n";
  echo __LINE__ . "：网址 - 采集规则丨{$obj->url} - {$obj->name}", "<br><br>\n\n";

  if (isset($obj->subMap)) {
    echo __LINE__ . "：子任务入库", "<br><br>\n\n";
    foreach ($obj->subMap as $opt) {
      if (!$project->isset($opt['with']))
        continue;
      $getOpt = array("cur" => $task['name'], "project" => $task['project']) + $opt;
      $list = $obj->Get($getOpt);
      $rlt = kumo_AddRisuto($list);
      echo __LINE__ . "：{$opt['with']}-{$rlt}", "<br><br>\n\n";
    }
    return;
  }
  // return;
  if (isset($obj->act)) {
    $data = $obj->Get();
    kumo_DoAct($data, $obj->act);
    return;
  }
}
// RunTime();
