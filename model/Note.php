<?php

namespace addons\notes\model;

use think\Model;

/**
 * 笔记模型
 */
class Note Extends Model
{

    protected $name = "notes_note";
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [];
    protected static $config = [];

    //自定义初始化
    protected static function init()
    {
        $config = get_addon_config('notes');
        self::$config = $config;
    }

    public function getStatusList()
    {
        return [
            '1' => __('Normal'), 
            '0' => __('Hidden'), 
        ];
    }

    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * 关联管理员模型
     */
    public function admin()
    {
        return $this->belongsTo('\app\admin\model\Admin', 'admin_id', 'id')->setEagerlyType(1);
    }

}
