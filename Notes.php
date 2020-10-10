<?php

namespace addons\notes;

use think\Addons;

use app\common\library\Menu;

/**
 * 后台笔记
 */
class Notes extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu =
            [
                [
                    "name"    => "notes",
                    "title"   => "后台笔记",
                    "icon"    => "fa fa-list",
                    "ismenu"  => 1,
                    "sublist" => [
                        [
                            "name"    => "notes/note",
                            "title"   => "笔记管理",
                            "icon"    => "fa fa-file",
                            "ismenu"  => 1,
                            "sublist" => [
                                [
                                    "name"  => "notes/note/index",
                                    "title" => "首页"
                                ],
                                [
                                    "name"  => "notes/note/add",
                                    "title" => "添加"
                                ],
                                [
                                    "name"  => "notes/note/edit",
                                    "title" => "编辑"
                                ],
                                [
                                    "name"  => "notes/note/del",
                                    "title" => "删除"
                                ],
                                [
                                    "name"  => "notes/note/multi",
                                    "title" => "批量更新"
                                ]
                            ]
                        ],
                        [
                            "name"    => "notes/tag",
                            "title"   => "笔记标签",
                            "icon"    => "fa fa-tags",
                            "ismenu"  => 1,
                            "sublist" => [
                                [
                                    "name"  => "notes/tag/index",
                                    "title" => "首页"
                                ],
                                [
                                    "name"  => "notes/tag/add",
                                    "title" => "添加"
                                ],
                                [
                                    "name"  => "notes/tag/edit",
                                    "title" => "编辑"
                                ],
                                [
                                    "name"  => "notes/tag/del",
                                    "title" => "删除"
                                ],
                                [
                                    "name"  => "notes/tag/multi",
                                    "title" => "批量更新"
                                ]
                            ]
                        ],
                        [
                            "name"    => "notes/fast",
                            "title"   => "快速记录",
                            "ismenu"  => 0,
                            "sublist" => [
                                [
                                    "name"  => "notes/fast/index",
                                    "title" => "首页"
                                ],
                                [
                                    "name"  => "notes/fast/note",
                                    "title" => "添加/编辑"
                                ],
                                [
                                    "name"  => "notes/fast/del",
                                    "title" => "删除"
                                ],
                            ]
                        ],
                    ]
                ]
            ];
        Menu::create($menu);
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('notes');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('notes');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('notes');
        return true;
    }

}
