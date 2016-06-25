$(function (e) {
    var yboard = function () {
    };
    $.extend(yboard.prototype, {
        config: {
            tableId: '#table'
        },
        /**
         * 初始化函数
         */
        init: function () {
            var _this = this;
            if (arguments.length == 1 && $.isPlainObject(options)) {
                _this.config = $.extend(_this.config, arguments[0]);
            }
            //绑定按钮点击事件
            _this.bindBtnClick();

            $('input[type=file]').ace_file_input({
                no_file: '文件上传 ...',
                btn_choose: '选择',
                btn_change: '更改',
                droppable: false,
                onchange: null,
                thumbnail: false
            });
            $('.date-picker').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: 'yyyy-mm-dd'
                })
                //show datepicker when clicking on the icon
                .next().on(ace.click_event, function () {
                $(this).prev().focus();
            });
            $('.input-daterange').datepicker({autoclose: true, format: 'yyyy-mm-dd', todayHighlight: true});
        },
        bindBtnClick: function () {
            var _this = this;
            //弹窗添加内容
            var _addForm = $('button#addForm');
            if (_addForm.length) {
                _addForm.on(ace.click_event, function () {
                    if ($.isFunction(window.addForm)) {
                        addForm(_addForm);
                        return true;
                    }
                    var _dialog = _addForm.attr('data-dialog');
                    //点击添加按钮
                    if ($.isFunction(window.getFormData)) {
                        getFormData(_addForm, function (formData) {
                            $.showDialog(_addForm.attr('dialog-title'), template(_dialog, {data: formData}), function (e) {
                                $(e).find('form').ajaxForms(function () {
                                    bootbox.hideAll();
                                    if ($(_this.config.tableId).length > 0) {
                                        $(_this.config.tableId).bootstrapTable('refresh');
                                    }
                                    return true;
                                });
                                return false;
                            });
                        });
                    }
                });
            }
            //删除
            var _delForm = $('button#delForm');
            if (_delForm.length) {
                _delForm.on(ace.click_event, function () {

                    var _pk = _delForm.attr('data-pk');
                    ids = $(_this.config.tableId).getListIds(_pk);
                    if (!ids) {
                        return false;
                    }
                    $.showConfirm('是否要进行数据删除操作?', function () {
                        $.postOption(_delForm.attr('data-action'), {ids: ids});
                        return false;
                    });
                });
            }
            var _editForm = $('button#editForm');
            if (_editForm.length) {
                _editForm.on(ace.click_event, function () {
                    if ($.isFunction(window.editForm)) {
                        editForm(_editForm);
                        return true;
                    }
                    var _dialog = _editForm.attr('data-dialog');
                    //点击添加按钮
                    if ($.isFunction(window.getFormData)) {
                        getFormData(_editForm, function (formData) {
                            $.showDialog(_editForm.attr('dialog-title'), template(_dialog, {data: formData}), function (e) {
                                $(e).find('form').ajaxForms(function () {
                                    bootbox.hideAll();
                                    if ($(_this.config.tableId).length > 0) {
                                        $(_this.config.tableId).bootstrapTable('refresh');
                                    }
                                    return true;
                                });
                                return false;
                            });
                        });
                    }
                });
            }

            //submit提交
            var _submit = $('button[type=submit]');
            if (_submit.length > 0) {
                _submit.on(ace.click_event, function (e) {
                    e.preventDefault();
                    _submit.closest('form').ajaxForms();
                });
            }

            //点击返回按钮
            var _reply = $('button#reply');
            if (_reply.length > 0) {
                _reply.on(ace.click_event, function () {
                    var href = $(this).attr('data-action');
                    if (href.length && href) {
                        window.location.href = href;
                    } else {
                        history.back(-1);
                    }
                });
            }
            //点击查询按钮
            var _search = $('button#searchBtn');
            if (_search.length > 0) {
                _search.on(ace.click_event, function () {
                    var params = $(_this.config.tableId).bootstrapTable('getOptions');
                    params.queryParams = function (params) {
                        //定义参数
                        var search = {};
                        form = _search.closest('form');
                        $.each(form.serializeArray(), function (i, field) {
                            search[field.name] = field.value;
                        });
                        //参数转为json字符串，并赋给search变量 ,JSON.stringify <ie7不支持，有第三方解决插件
                        params.search = JSON.stringify(search);
                        return params;
                    };

                    $(_this.config.tableId).bootstrapTable('refresh', params);
                });
            }
            
            $('#loginOut').on(ace.click_event, function (e) {
                $.post($(this).attr('data-url'), {}, function (data) {
                    if (data.code == 200) {
                        $.popMessage('ok', data.message);
                        if (data.url) {
                            setTimeout(function () {
                                window.location.href = data.url;
                            }, 500);
                            return true;
                        }
                    } else {
                        $.popMessage('error', data.message);
                    }
                }, 'json');
            })
        }
    });
    window.yboard = new yboard();
});