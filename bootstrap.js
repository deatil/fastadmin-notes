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
            'notes': '../addons/notes/js/index',
        },
        shim: {
            'notes': {
                deps: ['css!../addons/notes/css/style.css'],
                exports: 'Notes'
            },
        }
    });
    require(['notes']);
}
