<?php

namespace addons\notes\validate;

use think\Validate;

class Tag extends Validate
{

    /**
     * 验证规则
     */
    protected $rule = [
        'name' => 'require|unique:NotesTag',
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
        'add'  => ['name'],
        'edit' => ['name'],
    ];

    public function __construct(array $rules = [], $message = [], $field = [])
    {
        $this->field = [
            'name' => __('Notes Tag Name'),
        ];
        parent::__construct($rules, $message, $field);
    }

}
