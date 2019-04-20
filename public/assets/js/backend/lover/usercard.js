define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lover/usercard/index',
                    add_url: 'lover/usercard/add',
                    edit_url: 'lover/usercard/edit',
                    del_url: 'lover/usercard/del',
                    multi_url: 'lover/usercard/multi',
                    table: 'lover_user_card',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'lover_user_id', title: __('Lover_user_id')},
                        {field: 'type', title: __('Type'), searchList: {"text":__('Type text'),"images":__('Type images'),"question":__('Type question')}, formatter: Table.api.formatter.normal},
                        {field: 'placeholder', title: __('Placeholder')},
                        {field: 'topic', title: __('Topic'), searchList: {"about":__('Topic about'),"edu":__('Topic edu'),"family":__('Topic family'),"half":__('Topic half'),"love":__('Topic love'),"hobby":__('Topic hobby')}, formatter: Table.api.formatter.normal},
                        {field: 'title', title: __('Title')},
                        {field: 'contenttext', title: __('Contenttext')},
                        {field: 'contentimages', title: __('Contentimages'), formatter: Table.api.formatter.images},
                        {field: 'isshow', title: __('Isshow'), searchList: {"0":__('Isshow 0'),"1":__('Isshow 1')}, formatter: Table.api.formatter.normal},
                        {field: 'isdefault', title: __('Isdefault'), searchList: {"0":__('Isdefault 0'),"1":__('Isdefault 1')}, formatter: Table.api.formatter.normal},
                        {field: 'weigh', title: __('Weigh')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
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