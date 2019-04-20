define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lover/basicinfo/index',
                    add_url: 'lover/basicinfo/add',
                    edit_url: 'lover/basicinfo/edit',
                    del_url: 'lover/basicinfo/del',
                    multi_url: 'lover/basicinfo/multi',
                    table: 'lover_user_basicinfo',
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
                        {field: 'phone', title: __('Phone')},
                        {field: 'nickname', title: __('Nickname')},
                        {field: 'signature', title: __('Signature')},
                        {field: 'about', title: __('About')},
                        {field: 'wechatid', title: __('Wechatid')},
                        {field: 'avatar', title: __('Avatar')},
                        {field: 'photos', title: __('Photos'),formatter:Table.api.formatter.images},
                        {field: 'gender', title: __('Gender'), searchList: {"0":__('Gender 0'),"1":__('Gender 1'),"2":__('Gender 2')}, formatter: Table.api.formatter.normal},
                        {field: 'birthday', title: __('Birthday'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'constellation', title: __('Constellation')},
                        {field: 'height', title: __('Height')},
                        {field: 'weight', title: __('Weight')},
                        {field: 'company', title: __('Company')},
                        {field: 'industry', title: __('Industry')},
                        {field: 'position', title: __('Position')},
                        {field: 'isabroad', title: __('Isabroad'), searchList: {"0":__('Isabroad 0'),"1":__('Isabroad 1')}, formatter: Table.api.formatter.normal},
                        {field: 'abroadcountry', title: __('Abroadcountry')},
                        {field: 'homeland_province', title: __('Homeland_province')},
                        {field: 'homeland_city', title: __('Homeland_city')},
                        {field: 'living_province', title: __('Living_province')},
                        {field: 'living_city', title: __('Living_city')},
                        {field: 'highestschool', title: __('Highestschool')},
                        {field: 'highestdegree', title: __('Highestdegree'), searchList: {"0":__('Highestdegree 0'),"2":__('Highestdegree 2'),"3":__('Highestdegree 3')}, formatter: Table.api.formatter.normal},
                        {field: 'annualincome', title: __('Annualincome'), searchList: {"0":__('Annualincome 0'),"1":__('Annualincome 1'),"2":__('Annualincome 2'),"3":__('Annualincome 3'),"4":__('Annualincome 4')}, formatter: Table.api.formatter.normal},
                        {field: 'marital', title: __('Marital'), searchList: {"0":__('Marital 0'),"1":__('Marital 1')}, formatter: Table.api.formatter.normal},
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