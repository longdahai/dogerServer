define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lover/formid/index',
                    add_url: 'lover/formid/add',
                    edit_url: 'lover/formid/edit',
                    del_url: 'lover/formid/del',
                    multi_url: 'lover/formid/multi',
                    table: 'lover_user_formid',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'form_id',
                sortName: 'form_id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'form_id', title: __('Form_id')},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'expiretime', title: __('Expiretime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'expiretime_text', title: __('Expiration'),searchList: {"expired": '已过期', "canuse":'可用'},formatter:Table.api.formatter.status},
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons:[
                                {
                                    name: 'send',
                                    text: __('发送服务通知'),
                                    title: __('发送服务通知'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'example/bootstraptable/detail',
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                }
                            ]
                        }

                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});