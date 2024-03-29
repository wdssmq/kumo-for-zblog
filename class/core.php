<?php

/**
 * 核心处理
 * 咸鱼采集（https://github.com/wdssmq/kumo-for-zblog）
 * 沉冰浮水
 * 2020-08-01 ——
 */

use QL\QueryList;

// 抓取远程内容并解析出所需数据
class kumoCore
{
  public $Query;
  // public $Result = array();
  protected $data = array();
  protected $errinfo = array(
    0 => "",
    1 => "html 为空",
    2 => "图片获取失败",
  );
  protected $errcode = 0;
  public function __construct($option, $url = "")
  {
    // $this->data["act"] = array();
    foreach ($option as $key => $value) {
      $this->data[$key] = $value;
    }
    if (empty($url)) {
      $this->url = $option["url"];
    } else {
      $this->url = $url;
    }
    try {
      $this->Query = QueryList::get($this->url, null, [
        'cache' => $this->cache,
        'cache_ttl' => 3600 // 缓存有效时间，单位：秒，可以不设置缓存有效时间
      ]);
    } catch (Exception $e) {
      $this->errcode = 1;
    }
    // $temp = array_slice(explode("/", $this->url), 0, 3);
    // $temp[] = "";
    // $this->host = implode("/", $temp);
    // $html = $this->http($this->url);
    // if (empty($html)) {
    //   $this->errcode = 1;
    // } else {
    //   $this->Query = QueryList::html($html);
    // }
  }
  /**
   *
   * @param array $option
   * @param bool $type 0|1 ，获取新列表页链接或解析当前内容
   * @return void
   */
  public function Get($option, $type)
  {
    if ($this->errcode > 0)
      return null;
    if ($type === 0) {
      if (!isset($option["host"])) {
        $option["host"] = "";
      }
      $curKey = $option['pick'];
      $option["rule"] = array($curKey => $this->rules[$curKey]);
      $cQuery = $this->Query->rules($option["rule"])->range($option["range"])->query();
      $data = $cQuery->getData(function ($item) use ($option, $curKey) {
        $rlt = $option;
        if (isset($item[$curKey])) {
          $rlt["url"] = $option["host"] . $item[$curKey];
        }
        return $rlt;
      })->all();
      // $data = $cQuery->getData()->all();
    } elseif ($type === 1) {
      $act = $this->act;
      $data = $this->Query->rules($this->rules)->range("body")->queryData();
      if (empty($data)) {
        echo __LINE__ . "：解析为空\n\n";
        return $data;
      }
      if (isset($data[0])) {
        $data = $data[0];
      }
      foreach ($act as $k => $a) {
        if (HasNameInString($a[0], "range")) {
          $curRule = $this->data[$a[2]];
          $cQuery = $this->Query->rules($curRule)->range($a[1])->query();
          // $data[$k] = $this->Query->find($rule[0])->texts()->all();
          $data[$k] = $cQuery->getData()->all();
        } elseif (HasNameInString($a[0], "tpl")) {
          if (HasNameInString($a[0], "array")) {
            $data[$k] = "";
            foreach ($data[$a[2]] as $key => $value) {
              $tmp = $a[1];
              foreach ($value as $subk => $subv) {
                $tmp = str_replace("+{$subk}+", $subv, $tmp);
              }
              $data[$k] .= $tmp;
            }
          } else {
            $data[$k] = $a[1];
            foreach ($data as $key => $value) {
              $data[$k] = str_replace("+{$key}+", $value, $data[$k]);
            }
          }
        } elseif (!isset($data[$k])) {
          $data[$k] = HasNameInString($a[0], "str") ? $a[1] : $data[$a[1]];
        }
      }
    }

    if (isset($option["debug"]) && $option["debug"] == 1) {
      // debug
      // ob_clean();
      echo __FILE__ . "丨" . __LINE__ . ":<br>\n";
      var_dump($data);
      echo "<br><br>\n\n";
      die();
      // debug
    }

    return $data;
  }
  private function http($url, $p = "")
  {
    $dir = $this->cache;
    $file = "{$dir}{$p}_" . urlencode($url);
    if (is_file($file))
      return file_get_contents($file);
    $http = Network::Create();
    $http->open('GET', $url);
    // $http->setRequestHeader('User-Agent', 'curl');
    $http->send();
    if ($http->status == 200) {
      $str = str_replace($this->host, '-host-', $http->responseText);
      $str = str_replace('src="/', 'src="-host-', $str);
      $str = str_replace('href="/', 'href="-host-', $str);
      $str = str_replace('-host-/', "//", $str);
      $str = str_replace('-host-', $this->host, $str);
      file_put_contents($file, $str);
      return $str;
    } else {
      return "";
    }
  }
  /**
   * @param $name
   * @param $value
   */
  public function __set($name, $value)
  {
    switch ($name) {
        // case 'url':
        // case 'name':
        // case 'cache':
      case 'data':
      case 'ErrCode':
      case 'ErrInfo':
        return;
        break;
    }
    if (isset($this->$name) && !isset($this->data[$name])) {
      return;
    }
    $this->data[$name] = $value;
  }
  /**
   * @param $name
   *
   * @return mixed
   */
  public function __get($name)
  {
    switch ($name) {
      case 'ErrCode':
        return $this->errcode;
        break;
      case 'ErrInfo':
        return $this->errinfo[$this->errcode];
        break;
      case 'ViewData':
        return $this->Query->getData();;
        break;
    }
    if (isset($this->data[$name])) {
      return $this->data[$name];
    } else {
      return null;
    }
  }
  /**
   * @param $name
   *
   * @return bool
   */
  public function __isset($name)
  {
    return isset($this->data[$name]);
  }
  /**
   * @param $name
   */
  public function __unset($name)
  {
    unset($this->data[$name]);
  }
  /**
   * 获取数据库数据.
   *
   * @return array
   */
  public function GetData()
  {
    $data = $this->data;
    return $data;
  }
  public function debug($d = null)
  {
    echo "<br>" . __LINE__ . ":<br>\n";
    var_dump($this->url);
    echo "<br>\n";
    echo "<br>" . __LINE__ . ":<br>\n";
    var_dump($this->name);
    echo "<br>\n";
    if ($this->errcode > 0) {
      echo "<br>" . __LINE__ . ":<br>\n";
      var_dump($this->ErrInfo);
      echo "<br>\n";
      var_dump($this->GetData());
    } else {
      echo "<br>" . __LINE__ . ":<br>\n";
      var_dump($this->ViewData);
      var_dump($this->act);
    }
    if (!empty($d)) {
      die();
    }
  }
};
