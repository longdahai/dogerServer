define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lover/certification/index',
                    add_url: 'lover/certification/add',
                    edit_url: 'lover/certification/edit',
                    del_url: 'lover/certification/del',
                    multi_url: 'lover/certification/multi',
                    table: 'lover_user_certification',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'lover_user_id', title: __('Lover_user_id')},
                        {field: 'basicinfo.nickname', title: __('Basicinfo.nickname')},
                        {field: 'type', title: __('Type'), searchList: {"idcard":__('Type idcard'),"degree":__('Type degree'),"work":__('Type work'),"phone":__('Type phone')}, formatter: Table.api.formatter.normal},
                        {field: 'content', title: __('Content'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3')}, formatter: Table.api.formatter.status},
                        {field: 'remark', title: __('Remark')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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