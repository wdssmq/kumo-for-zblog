<?php
// 数据库入口，用于保存需要采集的URL、完成状态及其他信息
class kumo_Risuto extends Base
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
    if (!$zbp->db->ExistTable($zbp->table['kumo_Risuto'])) {
      $sql = $zbp->db->sql->CreateTable($zbp->table['kumo_Risuto'], $zbp->datainfo['kumo_Risuto']);
      $zbp->db->QueryMulit($sql);
      $zbp->SetHint('tips', "数据表已创建【kumo_Risuto】");
    }
  }
}
