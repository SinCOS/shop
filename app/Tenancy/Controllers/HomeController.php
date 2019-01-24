<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Box;
class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('仪表台')
            ->description('Description...')
            ->row('<h2 align=\'center\'>数据概览</a>')
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\User::count(), 'user', 'warning','/','
                    用户数'));
                });

                $row->column(4, function (Column $column) {
                    $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Order::count(), 'bill', 'warning', '/orders', '
                    订单数'));
                });
                $row->column(4,function(Column $column){
                     $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Shop::where('status',0)->count(), 'bill', 'danger', '/shops?status=0', '
                    店铺申请'));
                });
                 $row->column(4,function(Column $column){
                     $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Shop::where('status',1)->count(), 'bill', 'success', '/shops?status=1', '
                    正常运营'));
                });
                 $row->column(4,function(Column $column){
                     $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Shop::where('status',3)->count(), 'bill', 'danger', '/shops?status=3', '
                    关闭店铺'));
                });
                $row->column(4, function (Column $column) {
                    $column->append(new Box('版本信息',new Table([],[
                        '版本信息' => 1.1,
                        '联系我们' => "<a href='#'>休息下</a>"
                    ])));
                });
            });
    }
}
