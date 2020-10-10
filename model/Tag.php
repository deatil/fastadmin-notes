<?php

namespace addons\notes\model;

use think\Model;

/**
 * 标签
 */
class Tag Extends Model
{

    protected $name = "notes_tag";
    
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [];

    public function getStatusList()
    {
        return [
            '1' => __('Normal'), 
            '0' => __('Hidden'), 
        ];
    }

    public function createTags($note_id, $tags = [])
    {
        if (empty($note_id)) {
            return false;
        }
        
        if (empty($tags)) {
            return false;
        }
        
        foreach ($tags as $tag) {
            $tag_info = $this->where([
                'name' => $tag,
            ])->find();
            if (!empty($tag_info)) {
                $this->where([
                    'name' => $tag,
                ])->setInc('nums', 1);
                $tag_id = $tag_info['id'];
            } else {
                $tag_id = $this->insertGetId([
                    'name' => $tag,
                    'keywords' => $tag,
                    'description' => $tag,
                    'nums' => 1,
                    'status' => 1,
                    'add_time' => time(),
                    'add_ip' => request()->ip(1),
                ]);
            }
            
            if ($tag_id !== false) {
                NoteTag::where([
                    'note_id' => $note_id,
                ])->delete();
                
                NoteTag::create([
                    'note_id' => $note_id,
                    'tag_id' => $tag_id,
                    'add_time' => time(),
                    'add_ip' => request()->ip(1),
                ]);
            }
        }
    }

    public function updateTagsNums($tags = [], $num = 1)
    {
        if (empty($tags)) {
            return false;
        }
        
        foreach ($tags as $tag) {
            if ($num > 0) {
                $this->where([
                    'name' => $tag,
                ])->setInc('nums', $num);
            } else {
                $this->where([
                    'name' => $tag,
                ])->setDec('nums', abs($num));
            }
        }
    }
    
    public function deleteNoteTagByNoteids($ids = "") 
    {
        if (empty($ids)) {
            return false;
        }
        
        $tags = NoteTag::whereIn('note_id', $ids)->select();
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                Tag::where('id', $tag['tag_id'])->setDec('nums', 1);
            }
        }
        
        NoteTag::whereIn('note_id', $ids)->delete();
    }

}
