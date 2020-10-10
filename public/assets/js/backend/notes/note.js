define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'notes/note/index',
                    add_url: 'notes/note/add',
                    edit_url: 'notes/note/edit',
                    del_url: 'notes/note/del',
                    multi_url: 'notes/note/multi',
                }
            });

            var table = $("#table");

            //在表格内容渲染完成后回调的事件
            table.on('post-body.bs.table', function (e, json) {
                $("tbody tr[data-index]", this).each(function () {
                    // $("input[type=checkbox]", this).prop("disabled", true);
                });
            });

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID', sortable: true},
                        {
                            field: 'content', 
                            title: __('Content'),
                            formatter: Controller.api.formatter.content
                        },
                        {
                            field: 'tags', 
                            title: __('Notes Tags'), 
                            formatter: Controller.api.formatter.tags
                        },
                        {field: 'admin.username', title: __('Username'), operate:false, formatter: Table.api.formatter.label},
                        {field: 'status', title: __("Status"), searchList: {"1":__('Normal'),"0":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'edit_time', title: __('Update time'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {
                            field: 'operate', 
                            title: __('Operate'), 
                            table: table, 
                            events: Table.api.events.operate, 
                            buttons: [
                                {
                                    name: 'detail',
                                    title: __('Notes detail'),
                                    extend: 'data-toggle="tooltip"',
                                    classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                                    icon: 'fa fa-file',
                                    url: 'notes/note/detail',
                                    callback: function (data) {
                                        table.bootstrapTable('refresh');
                                    }
                                }
                            ],
                            formatter: function (value, row, index) {
                                return Table.api.formatter.operate.call(this, value, row, index);
                            }
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Controller.api.bindevent();
            Form.api.bindevent($("form[role=form]"));
        },
        api: {
            formatter: {
                content: function (value, row, index) {
                    var is_top = row['is_top'];
                    if (is_top == 1) {
                        value = '<span class="text-danger" title="'+__('Note top')+'"><i class="fa fa-arrow-circle-up"></i></span> ' + value;
                    }
                    
                    return value;
                },
                tags: function (value, row, index) {
                    if (!value) {
                        return '';
                    }
                    var valueArr = value.toString().split(/,/);
                    var result = [];
                    $.each(valueArr, function (i, j) {
                        var tpl = '<span class="label label-info">' + j + '</span>';
                        result.push(tpl);
                    });
                    return result.join(' ');
                }
            },
            bindevent: function () {
                require(['notes-jquery-tagsinput'], function () {
                    //标签输入
                    var elem = "#n-tags";
                    var tags = $(elem);
                    tags.tagsInput({
                        width: 'auto',
                        defaultText: '输入后空格确认',
                        minInputWidth: 110,
                        height: '36px',
                        placeholderColor: '#999',
                        onChange: function (row) {
                            if (typeof callback === 'function') {

                            } else {
                                $(elem + "_addTag").focus();
                                $(elem + "_tag").trigger("blur.autocomplete").focus();
                            }
                        },
                        autocomplete: {
                            url: 'notes/note/index',
                            minChars: 1,
                            menuClass: 'autocomplete-tags'
                        }
                    });
                });
            }
        }
    };
    return Controller;
});