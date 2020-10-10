<?php

namespace addons\notes\validate;

use think\Validate;

class Note extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'admin_id' => 'require',
        'content' => 'require',
    ];

    /**
     * 提示消息
     */
    protected $message = [
    ];

    /**
     * 字段描述
     */
    protected $field = [
    ];

    /**
     * 验证场景
     */
    protected $scene = [
        'add'  => ['admin_id', 'content'],
        'edit' => ['content'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'admin_id' => __('Notes AdminId'),
            'content' => __('Notes Content'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
