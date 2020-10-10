require.config({
    paths: {
        'notes-jquery-autocomplete': '../addons/notes/js/jquery.autocomplete',
        'notes-jquery-tagsinput': '../addons/notes/js/jquery.tagsinput',
    },
    shim: {
        'notes-jquery-tagsinput': {
            deps: ['jquery', 'notes-jquery-autocomplete', 'css!../addons/notes/css/jquery.tagsinput.min.css'],
            exports: '$.fn.extend'
        },
    }
});
if (Config.controllername == 'index' && Config.actionname == 'index') {
    require.config({
        paths: {
            'notes': '../addons/notes/js/notes',
        },
        shim: {
            'notes': {
                deps: ['css!../addons/notes/css/style.css'],
                exports: 'Notes'
            },
        }
    });
    
    require(['notes'], function (undefined) {
        var html = 
            '<div class="notes-box">' 
                + '<div class="notes-btns hide">'
                    + '<a href="javascript:;" class="notes-fast-plus js-notes-fast-add">'
                        + '<i class="fa fa-plus" title="' + __('Notes Add Note') +'"></i>'
                    + '</a>'
                    + '<a href="javascript:;" class="notes-fast-list js-notes-fast-list">'
                        + '<i class="fa fa-file" title="' + __('Notes Note List') +'"></i>'
                    + '</a>'
                + '</div>'
                + '<div class="notes-btn">'
                    + '<a href="javascript:;" class="notes-fast-toggle js-notes-fast-toggle">'
                        + '<i class="fa fa-pencil" title="' + __('Notes Note') +'"></i>'
                    + '</a>'
                + '</div>'
            '</div>';
        
        $("body").append(html);
        
        $("body").on('click', '.js-notes-fast-toggle', function() {
            if ($('.notes-btns').hasClass('hide')) {
                $('.notes-btns').removeClass('hide');
                $(this).addClass('active');
            } else {
                $('.notes-btns').addClass('hide');
                $(this).removeClass('active');
            }
        });
        
        $("body").on('click', '.js-notes-fast-add', function() {
            var offset = $(this).offset();
            var pt = offset.top;
            var pl = offset.left;
            var ph = $(this).height();
            var pw = $(this).width();
            var pph = Number($(this).height()) + 80;
            var ppw = Number($(this).width()) + 620;
            var ppt = (pt + ph/2 - pph/2) + 'px';
            var ppl = (pl + pw/2 - ppw/2) + 'px';
            
            var note_id = 0;
            
            Layer.open({
                title: __('Notes'),
                type: 1,
                shade: 0,
                closeBtn: 1,
                shadeClose: 0,
                offset: [ppt, ppl],
                anim: -1,
                id: "notes-note",
                skin: "layui-layer-lan layer-notes-note",
                content: '<textarea placeholder="'+__('Notes Content')+'" class="notes-fast-note js-notes-fast-note"></textarea>',
                resize: !1,
                success: function(e, a) {
                    var i = e.find("textarea");
                    i.val('').focus().on("keyup", function() {
                        
                    })
                }
            });
        });
        
        $("body").on('click', '.js-notes-fast-note-item', function() {
            var note_id = $(this).data('id');
            var note_content = oldContent = $(this).find('.notes-fast-note-item-content').html();
            
            Layer.open({
                title: __('Notes'),
                type: 1,
                shade: 0,
                closeBtn: 1,
                shadeClose: 0,
                offset: 'center',
                anim: -1,
                id: "notes-note",
                skin: "layui-layer-lan layer-notes-note",
                content: '<textarea placeholder="'+__('Notes Content')+'" class="notes-fast-note js-notes-fast-note" data-noteid="'+note_id+'"></textarea>',
                resize: !1,
                success: function(e, a) {
                    var i = e.find("textarea");
                    i.val(note_content);
                }
            });
        });
        
        var oldContent = '';
        $("body").on('blur', '.js-notes-fast-note', function() {
            var thiz = this;
            var content = $(this).val();
            var fastNoteId = $(this).data('noteid');
            
            if (oldContent != content) {
                oldContent = content;
                $.ajax({
                    url: 'notes/fast/note',
                    type: 'POST',
                    data: {
                        'row': {
                            'id': fastNoteId,
                            'content': content,
                        }
                    },
                    dataType: 'json',
                    cache: false,
                    success: function (ret) {
                        if (ret.hasOwnProperty("code")) {
                            var msg = ret.hasOwnProperty("msg") && ret.msg != "" ? ret.msg : "";
                            if (ret.code == 1) {
                                if (!fastNoteId) {
                                    $(thiz).data('noteid', ret.data.id)
                                }
                            } else {
                                Toastr.error(msg ? msg : __('Fast note failed'));
                            }
                        } else {
                            Toastr.error(__('Unknown data format'));
                        }
                    },
                    error: function () {
                        Toastr.error(__('Network error'));
                    }
                });
            }
        });
        
        var area = [$(window).width() > 350 ? '300px' : '95%', $(window).height() > 600 ? '100%' : '95%'];
        $("body").on('click', '.js-notes-fast-list', function() {
            var html = '';
            var data = '';
            
            $.ajax({
                url: 'notes/fast/index',
                type: 'GET',
                data: {},
                dataType: 'json',
                cache: false,
                async: false,
                success: function (ret) {
                    if (ret.hasOwnProperty("code")) {
                        var msg = ret.hasOwnProperty("msg") && ret.msg != "" ? ret.msg : "";
                        if (ret.code == 1) {
                            data = ret.rows;
                        } else {
                            Toastr.error(msg ? msg : __('Fast note failed'));
                        }
                    } else {
                        Toastr.error(__('Unknown data format'));
                    }
                },
                error: function () {
                    Toastr.error(__('Network error'));
                }
            });
            
            var formatter = {
                datetime: function (value) {
                    var datetimeFormat = typeof this.datetimeFormat === 'undefined' ? 'YYYY-MM-DD HH:mm:ss' : this.datetimeFormat;
                    if (isNaN(value)) {
                        return value ? Moment(value).format(datetimeFormat) : __('None');
                    } else {
                        return value ? Moment(parseInt(value) * 1000).format(datetimeFormat) : __('None');
                    }
                }
            };
            
            $(data).each(function(index, edata) {
                var content = edata.content;
                if (edata.is_top == 1) {
                    content = '<span class="text-danger" title="'+__('Note top')+'"><i class="fa fa-arrow-circle-up"></i></span> ' + content;
                }
                
                html += '<li class="notes-fast-note-item js-notes-fast-note-item notes-fast-note-item-' + edata.id + '" data-id="' + edata.id + '">'
                    + '<div class="notes-fast-note-item-date">'
                        + '<span class="notes-fast-note-item-time">'
                        + formatter.datetime(edata.edit_time)
                        + '</span>'
                        + '<a href="javascript:;" class="notes-fast-note-item-remove js-notes-fast-note-item-remove" data-id="'+edata.id+'" title="'+__('Notes remove note')+'">'
                            + '<i class="fa fa-trash"></i>'
                        + '</a>'
                    + '</div>'
                    + '<div class="notes-fast-note-item-content">'
                    + content
                    + '</div>'
                + '</li>';
            });
            
            html = '<div class="notes-fast-note-title">'+__('Notes new note list')+'</div><ul>' + html + '</ul>';
            
            layer.open({
                type: 1,
                id: "notes-fast-list",
                anim: -1,
                title: !1,
                closeBtn: !1,
                offset: "r",
                shade: .1,
                shadeClose: !0,
                skin: "layui-anim layui-anim-rl notes-fast-list",
                area: "300px",
                content: html,
            });
        });
        
        $("body").on('click', '.js-notes-fast-note-item-remove', function() {
            var thiz = this;
            var id = $(this).data('id');
            
            Layer.confirm(__('Notes now remove this note?'), {title: __('Notes remove note tip'), icon: 3}, function (index) {
                Fast.api.ajax({
                    url: "notes/fast/del",
                    data: {ids: id}
                }, function (data, ret) {
                    if (ret.hasOwnProperty("code")) {
                        var msg = ret.hasOwnProperty("msg") && ret.msg != "" ? ret.msg : "";
                        if (ret.code == 1) {
                            $('.notes-fast-note-item-' + id).remove();
                            Fast.api.success(msg ? msg : __('Fast note success'));
                        } else {
                            Fast.api.error(msg ? msg : __('Fast note failed'));
                        }
                    } else {
                        Fast.api.error(__('Unknown data format'));
                    }
                    
                    Layer.close(index);
                    return false;
                }, function (data, ret) {
                    Fast.api.error(__('Network error'));
                    return false;
                });
            });
            
            return false;
        });
        
    });
}
