<?php
function kumo_Initialization(&$arrJSON = array())
{
  $GLOBALS['table']['kumo_Risuto'] = kumoDB::$tableX;
  $GLOBALS['datainfo']['kumo_Risuto'] = kumoDB::$datainfoX;
  $dir = kumo_Path("cache");
  if (!is_dir($dir)) {
    @mkdir(dirname($dir));
    @mkdir($dir, 0755);
    #code
  }
  $dir = kumo_Path("usr");
  $arrJSON = GetFilesInDir($dir, "json");
  $_SERVER['kumo_start_time'] = time();
}
function kumo_ReadJSON($name)
{
  $dirs = array("config" => kumo_Path("config"), "cache" => kumo_Path("cache"));
  $obj = new kumoJSON($name, $dirs);
  return $obj;
}
function kumo_AddRisuto($arrRisuto)
{
  $arrCount = [0, 0];
  foreach ($arrRisuto as $itemRisuto) {
    $obj = new kumoDB();
    if ($obj->LoadInfoByField("Url", $itemRisuto['url'])) {
      $repeat = 0;
      if ($_SERVER['kumo_start_time'] - $obj->Time > 259200) {
        $repeat++;
      }
      if ($repeat && isset($itemRisuto["repeat"]) && $itemRisuto["repeat"]) {
        $repeat++;
      }
      if ($repeat && $obj->With === $itemRisuto['cur']) {
        $repeat++;
      }
      if ($repeat >= 2) {
        $obj->Done = false;
        $obj->Time = $_SERVER['kumo_start_time'];
        echo  __LINE__ . "：{$obj->Url} - {$obj->With}", "<br>\n";
        echo  __LINE__ . "：索引页过期重置", "<br><br>\n\n";
        $arrCount[1]++;
      } else {
        $arrCount[0]++;
        continue;
      }
    } else {
      $arrCount[1]++;
    }
    $obj->Url = $itemRisuto['url'];
    $obj->With = $itemRisuto['with'];
    $obj->Project = $itemRisuto['project'];
    $obj->Save();
  }
  return "添加：{$arrCount[1]}丨已存在：{$arrCount[0]}";
}
function kumo_GetRisuto($opt = array())
{
  global $zbp;
  $w[] = array('=', 'rst_Done', 0);
  $w[] = array('=', 'rst_Project', $opt["project"]);
  if (isset($opt["order"])) {
    $w[] = array('=', 'rst_With', $opt["order"]);
  }
  $limit = isset($opt["num"]) ? $opt["num"] : 57;
  $sql = $zbp->db->sql->Select(kumoDB::$tableX, '*', $w, array('rst_ID' => 'asc'), $limit, null);
  $arr = $zbp->GetListType("kumoDB", $sql);
  $rlt = array();
  if (isset($opt["data"])) {
    foreach ($arr as $key => $Risuto) {
      $rlt[$Risuto->ID] = $Risuto->GetData();
    }
  } else {
    $rlt = $arr;
  }
  return $rlt;
}
function kumo_DoAct($arr, $act)
{
  $title = $arr["title"];
  $post = GetPost($title);
  $post->Title = $title;
  if (stripos($post->Content, $arr["body"]) === false)
    $post->Content .= $arr["body"];
  if (!isset($arr["cate"])) {
    echo __LINE__ . "未指定分类\n\n";
  }
  $post->PostTime = time();
  $post->CateID = kumo_GetCate($arr["cate"], "&gt;");
  $post->AuthorID = kumo_AuthorID($arr["author"]);
  // foreach ($act as $k => $itemRisuto) {
  //   if (HasNameInString($itemRisuto[0], "body")) {
  //     if (stripos($post->Content, $arr[$k]) === false)
  //       $post->Content .= $arr[$k];
  //   }
  //   if (HasNameInString($itemRisuto[0], "cate")) {
  //     $post->CateID = kumo_GetCate($arr[$k], "&gt;");
  //   }
  //   if (HasNameInString($itemRisuto[0], "author")) {
  //     $post->AuthorID = kumo_AuthorID($itemRisuto[1]);
  //   }
  // }
  return $post->Save();
}
function kumo_GetCate($name, $str = ">>")
{
  global $zbp;
  $arr = explode($str, $name);
  $ParentID = 0;
  foreach ($arr as $key => &$itemRisutoalue) {
    $itemRisutoalue = trim($itemRisutoalue);
    if (empty($itemRisutoalue))
      continue;
    $result = $zbp->GetCategoryList(null, array(
      array(
        '=',
        'cate_Name',
        $itemRisutoalue
      ),
      array(
        '=',
        'cate_ParentID',
        $ParentID
      )
    ), null, 1);
    if (count($result) == 0) {
      $cate = new Category();
      $cate->Name = $itemRisutoalue;
      $cate->ParentID = $ParentID;
      $cate->Save();
      $zbp->LoadCategorys();
    } else {
      $cate = $result[0];
    }
    $ParentID = $cate->ID;
  }
  return $cate->ID;
}
function kumo_AuthorID($name)
{
  global $zbp;
  $o = $zbp->GetMemberByName($name);
  if ($o->ID == 0) {
    $o->Name = $name;
    $o->Save();
    $zbp->LoadMembers();
  }
  return $o->ID;
}
