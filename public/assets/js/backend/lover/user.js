define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'lover/user/index',
                    add_url: 'lover/user/add',
                    edit_url: 'lover/user/edit',
                    del_url: 'lover/user/del',
                    multi_url: 'lover/user/multi',
                    table: 'lover_user',
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
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3')}, formatter: Table.api.formatter.status},
                        {field: 'is_new', title: __('Is_new'), searchList: {"0":__('Is_new 0'),"1":__('Is_new 1')}, formatter: Table.api.formatter.normal},
                        {field: 'cert_status', title: __('Cert_status'), searchList: {"0":__('Cert_status 0'),"1":__('Cert_status 1')}, formatter: Table.api.formatter.status},
                        {field: 'applytimes', title: __('Applytimes')},
                        {field: 'work_score', title: __('Work_score')},
                        {field: 'pretty_score', title: __('Pretty_score')},
                        {field: 'edu_score', title: __('Edu_score')},
                        {field: 'basicinfo.phone', title: __('Basicinfo.phone')},
                        {field: 'basicinfo.nickname', title: __('Basicinfo.nickname')},
                        {field: 'basicinfo.avatar', title: __('Basicinfo.avatar'), formatter: Table.api.formatter.image, operate: false},
                        {field: 'basicinfo.gender_text', title: __('Basicinfo.gender')},
                        {field: 'basicinfo.birthday', title: __('Basicinfo.birthday'), operate:'RANGE', addclass:'datetimerange'},
                        {field: 'basicinfo.homeland_province', title: __('Basicinfo.homeland_province')},
                        {field: 'basicinfo.homeland_city', title: __('Basicinfo.homeland_city')},
                        {field: 'basicinfo.living_province', title: __('Basicinfo.living_province')},
                        {field: 'basicinfo.living_city', title: __('Basicinfo.living_city')},
                        {field: 'basicinfo.highestdegree_text', title: __('Basicinfo.highestdegree')},
                        {field: 'basicinfo.annualincome_text', title: __('Basicinfo.annualincome')},
                        {field: 'basicinfo.marital_text', title: __('Basicinfo.marital')},
                        {field: 'createtime', title: __('createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {
                            field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'detail',
                                    text: __('完善基本资料'),
                                    title: __('完善基本资料'),
                                    classname: 'btn btn-xs btn-primary btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'lover/basicinfo/edit/bid/{basicinfo.id}',
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                },{
                                    name: 'detail',
                                    text: __('补充卡片'),
                                    title: __('补充卡片'),
                                    classname: 'btn btn-xs btn-warning btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'lover/usercard/edit/bid/{basicinfo.id}',
                                    callback: function (data) {
                                        Layer.alert("接收到回传数据：" + JSON.stringify(data), {title: "回传数据"});
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                }],
                            formatter: Table.api.formatter.operate
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