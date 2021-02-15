<?php
function kumo_Initialization(&$arrJSON = array())
{
  global $zbp;
  $dir = kumo_Path("cache");
  if (!is_dir($dir)) {
    @mkdir(dirname($dir));
    @mkdir($dir, 0755);
    #code
  }
  $dir = kumo_Path("usr");
  $arrJSON = GetFilesInDir($dir, "json");
  $_SERVER['kumo_start_time'] = time();
  $_SERVER['kumo_debug'] = (bool) $zbp->Config('kumo')->debug;
}
function kumo_debug($line, $msg, $content, $force = 0)
{
  if (!$_SERVER['kumo_debug'] && !$force) {
    return;
  }
  echo "-----<br><br>\n\n";
  echo "{$line}：{$msg}<br>{$content}", "<br><br>\n\n";
}
function kumo_ReadJSON($name)
{
  $dirs = array("proj" => kumo_Path("proj"), "cache" => kumo_Path("cache"));
  $obj = new kumoJSON($name, $dirs);
  return $obj;
}
function kumo_AddRisuto($arrRisuto)
{
  global $zbp;
  $arrCount = array("new" => 0, "skip" => 0);
  foreach ($arrRisuto as $itemRisuto) {
    $obj = new kumo_Risuto();

    if (empty($itemRisuto['url'])) {
      continue;
    }

    if ($obj->LoadInfoByField("Url", $itemRisuto['url'])) {
      $repeat = 0;
      if ($_SERVER['kumo_start_time'] - $obj->Time > 259200) {
        $repeat++;
      }
      if (isset($itemRisuto["repeat"])) {
        $repeat += $itemRisuto["repeat"];
      }
      if ($repeat && $obj->With === $itemRisuto['cur']) {
        $repeat++;
      }
      if ($repeat > 2) {
        $obj->Done = false;
        $obj->Time = $_SERVER['kumo_start_time'];
        kumo_debug(__LINE__, "索引页过期重置", "{$repeat}丨{$obj->Url} - {$obj->With}");
        $arrCount["new"]++;
        $obj->Save();
      } else {
        kumo_debug(__LINE__, "已存在", "{$obj->Url} - {$obj->With}<!--{$repeat}-->");
        $arrCount["skip"]++;
      }
      continue;
    } else {
      $arrCount["new"]++;
    }
    $obj->Url = $itemRisuto['url'];
    $obj->With = $itemRisuto['with'];
    $obj->Project = $itemRisuto['project'];
    $obj->Save();
    kumo_debug(__LINE__, "新增", "{$obj->Url} - {$obj->With}");
  }
  return "添加：{$arrCount["new"]}丨已存在：{$arrCount["skip"]}";
}
function kumo_GetRisuto($opt = array())
{
  global $zbp;
  $w[] = array('=', 'rst_Done', 0);
  $w[] = array('=', 'rst_Project', $opt["project"]);
  // if (isset($opt["order"])) {
  //   $w[] = array('=', 'rst_With', $opt["order"]);
  // }
  $limit = isset($opt["num"]) ? $opt["num"] : 57;
  $sql = $zbp->db->sql->Select($GLOBALS['table']['kumo_Risuto'], '*', $w, array('rst_ID' => 'asc'), $limit, null);
  $arr = $zbp->GetListType("kumo_Risuto", $sql);
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
  if (stripos($post->Content, $arr["body"]) === false) {
    $post->Content .= $arr["body"];
  } else {
    kumo_debug(__LINE__, "《{$post->Title}》已存在", "");
    return;
  }
  if (!isset($arr["cate"]) || $arr["cate"] == "") {
    echo __LINE__ . "：未指定分类\n\n";
  } else {
    $post->CateID = kumo_GetCate($arr["cate"]);
  }
  if (isset($arr['intro'])) {
    $post->Intro = $arr['intro'];
  } else if (empty($post->Intro)) {
    $post->Intro = preg_replace('/^((<p>.+?<\/p>){1,5}).+/u',  '$1', $post->Content);
    $post->Content = str_replace("<p></p>", "", $post->Content);
    $post->Content = str_replace("<p><br></p>", "", $post->Content);
    $post->Content = str_replace("<br></p>", "", $post->Content);
  }
  $post->PostTime = time();
  $post->AuthorID = kumo_AuthorID($arr["author"]);
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
