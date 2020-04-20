<?php // 基于JSON的站点任务配置
class kumoJSON
{
  public $name;
  public $dir;
  public $cache;
  public $file;
  protected $task = array();
  public function __construct($name, $dirs = array())
  {
    $this->name = $name;
    $this->dir = $dirs["config"];
    $this->cache =  $dirs["cache"];
    $this->file = $this->dir . "{$this->name}.json";
    if (is_file($this->file)) {
      $this->task = json_decode(file_get_contents($this->file), true);
    }
  }
  public function Add($opt)
  {
    $index = $opt["name"];
    unset($opt["name"]);
    unset($opt["cache"]);
    $this->task[$index] = $opt;
  }
  public function Save()
  {
    file_put_contents($this->file, json_encode($this->task));
  }
  public function isset($k)
  {
    return isset($this->task[$k]);
  }
  public function Get($k)
  {
    $item = $this->task[$k];
    $item["name"] = $k;
    $item["project"] = $this->name;
    $item["cache"] = $this->cache;
    return $item;
  }
  /**
   * @param $name
   *
   * @return mixed
   */
  public function __get($name)
  {
    if (isset($this->task[$name])) {
      return $this->task[$name];
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
    if (isset($this->task[$name])) {
      return true;
    } else {
      return false;
    }
  }
  // public function GetTask()
  // {
  //   $arr = $this->task;
  //   foreach ($arr as $n => &$t) {
  //     $t["name"] = $n;
  //     $t["cache"] = $this->cache;
  //   }
  //   return $arr;
  // }
};
