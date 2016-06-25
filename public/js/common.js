/**
 * Created by zouzehua on 2016-4-1.
 */
/**
 * Created by zouzehua on 2016-3-14.
 */
$.fn.extend({
    // 替换class
    replaceClass: function (replaceClass, stayClass) {
        var newClass = replaceClass;
        if (stayClass) {
            newClass += stayClass;
        }
        this.attr('class', newClass);
        return this;
    },
    //ajax提交form,这里可以允许两个回调参数，successFn,errorFn
    ajaxForms: function () {
        var numargs = arguments.length, successFn = '', errorFn = '';
        if (numargs > 3) {
            $.popMessage('error', 'ajaxForms方法最多允许三个参数');
            return;
        }
        if (numargs == 1 && $.isFunction(arguments[0])) {
            successFn = arguments[0];
        } else if (numargs == 2 && $.isFunction(arguments[1])) {
            errorFn = arguments[1];
        }
        var _this = this;
        if (_this.length > 0) {
            var options = {
                beforeSubmit: function (formData, jqForm, options) {
                    var isSuccess = true;
                    if ($.isFunction(window.validates)) {
                        isSuccess = validates(jqForm);
                    }
                    return isSuccess;
                },  // pre-submit callback
                success: function (data) {
                    if (data.code == 200) {
                        $.popMessage('ok', data.message);
                        _this.resetForm();
                        if (data.url) {
                            setTimeout(function () {
                                window.location.href = data.url;
                            }, 500);
                            return true;
                        }
                        if ($.isFunction(successFn)) {
                            successFn(data);
                        }
                    } else {
                        if ($.isFunction(errorFn)) {
                            errorFn(data);
                        }
                        $.popMessage('error', data.message);
                    }
                },  // post-submit callback

                // other available options:
                url: this.attr('action'),         // override for form's 'action' attribute
                type: 'post',       // 'get' or 'post', override for form's 'method' attribute
                dataType: 'json',       // 'xml', 'script', or 'json' (expected server response type)
                clearForm: false,      // clear all form fields after successful submit
                resetForm: false,       // reset the form after successful submit

                timeout: 3000
            };
            this.ajaxSubmit(options);
            return false;
        }
    },
    validates: function () {
        var numargs = arguments.length, _validateConf = {
            errorElement: 'div',
            errorClass: 'help-block',
            focusInvalid: false,
            ignore: "",
            highlight: function (e) {
                $(e).closest('.form-group').removeClass('has-info').addClass('has-error');
            },

            success: function (e) {
                $(e).closest('.form-group').removeClass('has-error');//.addClass('has-info');
                $(e).remove();
            },

            errorPlacement: function (error, element) {
                if (element.is('input[type=checkbox]') || element.is('input[type=radio]')) {
                    var controls = element.closest('div[class*="col-"]');
                    if (controls.find(':checkbox,:radio').length > 1) controls.append(error);
                    else error.insertAfter(element.nextAll('.lbl:eq(0)').eq(0));
                }
                else if (element.is('.select2')) {
                    error.insertAfter(element.siblings('[class*="select2-container"]:eq(0)'));
                }
                else if (element.is('.chosen-select')) {
                    error.insertAfter(element.siblings('[class*="chosen-container"]:eq(0)'));
                }
                else error.insertAfter(element.parent());
            },

            submitHandler: function (form) {
            },
            invalidHandler: function (form) {
            }
        };

        if (numargs == 1 && $.isPlainObject(arguments[0])) {
            _validateConf = $.extend(_validateConf, arguments[0]);
        }
        return this.validate(_validateConf).form();
    },
    //获取table里面的ids集合
    getListIds: function (pk) {
        var selections = this.bootstrapTable('getSelections');
        var length = selections.length, ids = [], id = pk || 'id';
        if (length > 0) {
            for (var i = 0; i < length; i++) {
                ids.push(selections[i][id]);
            }
        }
        if (ids.length < 1) {
            $.popMessage('error', '你还未选择操作项');
            return false;
        }
        //这里是接收第二个参数，如果为true的话，那么就是选择一个
        if (arguments.length == 2 && arguments[1]) {
            if (ids.length > 1) {
                $.popMessage('error', '只能选择一项进行操作');
                return false;
            }
            return ids[0];
        }
        return ids;//[2,14]
    },
    //使用方法$(tableid).getLists(fileds={}|''|'uid,content');参数可带可不带，参数可以是对象也可以是带,的字符串
    getLists: function () {
        var fields = '*';
        if (arguments.length > 0) {
            fields = arguments[0];
        }
        if (!$.isPlainObject(fields) && fields.indexOf(',') > 0) {
            fields = fields.split(',');
        }
        var selections = this.bootstrapTable('getSelections');
        var length = selections.length, arr = [];
        if (length > 0) {
            for (var i = 0; i < length; i++) {
                if (fields != '*') {
                    var _arr = {};
                    for (var j = 0; j < fields.length; j++) {
                        var _field = fields[j];
                        _arr[_field] = selections[i][_field];
                    }
                    arr.push(_arr);
                } else {
                    arr.push(selections[i]);
                }
            }
        }

        if (arr.length < 1) {
            $.popMessage('error', '你还未选择操作项');
            return false;
        }
        //这里是接收第二个参数，如果为true的话，那么就是选择一个
        if (arguments.length == 2 && arguments[1]) {
            if (arr.length > 1) {
                $.popMessage('error', '只能选择一项进行操作');
                return false;
            }
            return arr[0];//这里返回的是对象
        }

        return arr;
    }
});

$.extend({
    //标签蓝显
    hight_nav: function (url) {
        var _a = $('.nav-list').find('a[href="' + url + '"]');
        var _li = (arguments.length == 2 && arguments[1]) ? arguments[1] : _a.closest('li');
        if (_a.length > 0 && _li.length > 0) {
            _li.addClass('active');
        }
        if (_li.closest('.submenu').length > 0) {
            _li.closest('.submenu').closest('li').addClass('active open');
            $.hight_nav(url, _li.closest('.submenu').closest('li'));
        }
    },
    //message显示
    popMessage: function (type, message) {
        /**
         * type=[ok,error,warn]
         */
        if ($('#msg-pop').length == 0) {
            $('<div id="msg-pop" style="z-index:99999999;position: fixed;"><span id="msg-pop-body"></span></div>').appendTo('body');
        }
        $('#msg-pop-body').replaceClass('msg-pop-' + type).html(message);
        $('#msg-pop').show().delay(2000).fadeOut(500);
    },
    //确认框
    showConfirm: function (message, yesFn) {
        bootbox.confirm({
                message: message,
                buttons: {
                    confirm: {
                        label: "是",
                        className: "btn-primary btn-sm",
                    },
                    cancel: {
                        label: "否",
                        className: "btn-sm",
                    }
                },
                callback: function (result) {
                    if (result) {
                        yesFn();
                    }
                }
            }
        );
    },
    //弹窗添加信息，编辑信息
    showDialog: function (title, html, Fn) {
        if ($.isFunction(window.dialogShow)) {
            html += '<script type="text/javascript">dialogShow();</script>';
        }
        bootbox.dialog({
            title: title,
            message: html,
            buttons: {
                success: {
                    label: "提 交",
                    className: "btn-success",
                    callback: function () {
                        Fn(this);
                        return false;
                    }
                },
                cancel: {
                    label: "关 闭",
                    className: "btn-danger"
                }
            }
        });
    },
    loadDialog: function (html) {
        bootbox.dialog({
            message: '<i class="ace-icon fa fa-spinner fa-spin orange bigger-125"></i> ' + html,
            closeButton: false
        });
    },
    postOption: function (url, data) {
        $.post(url, data, function (data) {
            if (data.code == 200) {
                $.popMessage('ok', data.message);
                if (data.url) {
                    setTimeout(function () {
                        window.location.href = data.url;
                    }, 500);
                    return true;
                }
                $(yboard.config.tableId).bootstrapTable('refresh');
            } else {
                $.popMessage('error', data.message);
            }
        }, 'json');
    },
    showAlert: function (title, message) {
        bootbox.dialog({
            message: message,
            title: title,
            buttons: {
                main: {
                    label: "关 闭",
                    className: "btn-primary",
                    callback: function () {

                    }
                }
            }
        });
    }

});