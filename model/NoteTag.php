<?php

namespace addons\notes\model;

use think\Model;

/**
 * 笔记标签关联
 */
class NoteTag Extends Model
{
    protected $name = "notes_note_tag";
    
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [];
}
