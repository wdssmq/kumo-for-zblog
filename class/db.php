<?php
/*
 * list|1 : 列表页
 * post|2 : 文章页
 * postwithpage|3 :带分页的文章页
 *
 */

// 数据库入口，用于保存需要采集的URL、完成状态及其他信息
class kumoDB extends Base
{
  public static $tableX = '%pre%kumo_Risuto';
  public static $datainfoX = array(
    'ID' => array('rst_ID', 'integer', '', 0),
    'Url' => array('rst_Url', 'string', 255, ''),
    'Project' => array('rst_Project', 'string', 37, ''),
    'With' => array('rst_With', 'string', 37, ''),
    'Done' => array('rst_Done', 'boolean', '', false),
    'Time' => array('rst_Time', 'integer', '', 0),
    'IP' => array('rst_IP', 'string', 50, '')
  );
  public function __construct()
  {
    global $zbp;
    parent::__construct($zbp->table['kumo_Risuto'], $zbp->datainfo['kumo_Risuto'], __CLASS__);
    $this->Time = time();
    $this->IP = GetGuestIP();
  }
  // Risuto::CreateTable()
  public static function CreateTable()
  {
    global $zbp;
    if (!$zbp->db->ExistTable(self::$tableX)) {
      $sql = $zbp->db->sql->CreateTable(self::$tableX, self::$datainfoX);
      $zbp->db->QueryMulit($sql);
    }
  }
}
