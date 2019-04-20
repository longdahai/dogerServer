define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lover/reports/index',
                    add_url: 'lover/reports/add',
                    edit_url: 'lover/reports/edit',
                    del_url: 'lover/reports/del',
                    multi_url: 'lover/reports/multi',
                    table: 'lover_reports',
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
                        {field: 'type', title: __('Type'), searchList: {"fraud":__('Fraud'),"porn":__('Porn'),"rude":__('Rude'),"others":__('Others')}, formatter: Table.api.formatter.normal},
                        {field: 'reportcontent', title: __('Reportcontent')},
                        {field: 'reportimages', title: __('Reportimages'), formatter: Table.api.formatter.images},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'dealcontent', title: __('Dealcontent')},
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