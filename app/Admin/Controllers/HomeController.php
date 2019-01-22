<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Table;
class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('资讯台');
            $content->description('Description...');

            $content->row('<h2 align="center">信息控制台</a>');
            if(\Admin::user()->isRole('operator')){
            $content->row(new Box('最近7天增长趋势图', view('admin.chart',['data' => \App\Admin\Model\OrderCommon::getDays(7),'height' => 400])));
            $content->row(function (Row $row) {

                

                $row->column(12, function (Column $column) {
                    // $order = \App\Models\Order::where('shop_id', \Admin::user()->shop_id)->select();
                    $column->append(new Box('订单统计',new Table(['待发货', '待收货物', '退款申请', '待付款'],[[0,0,0,0]])));
                });
                $row->column(12, function (Column $column) {
                    // $order = \App\Models\Order::where('shop_id', \Admin::user()->shop_id)->select();
                    $column->append(new Box('综合统计', new Table(['售完商品', '最近7天评论', '未上架商品'], [[0, 0, 0]])));
                });
               
                    $row->column(4, function (Column $column) {
                        $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Shop::find(\Admin::user()->shop_id)->money, 'bill', 'warning', '/orders', '
                        钱包'));
                    });
                
         
            });
        }elseif (\Admin::user()->isRole('agent')) {
            $content->row(new Box('欢迎使用','~~~~~'));
           $content->row(function(Row $row){
               $row->column(4,function($column){
                $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Shop::where('status',0)->count(), 'bill', 'warning', '/shops?status=0', '
                待审核商家'));
                
               });
               $row->column(4,function($column){
                $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Shop::where('status',1)->count(), 'bill', 'success', '/shops?status=1', '
                正常运营店铺'));
                
               });

               $row->column(4,function($column){
                $column->append(new \Encore\Admin\Widgets\InfoBox(\App\Models\Activity::count(), 'bill', 'info', '/activity', '
                活动统计'));
                
               });
           });
        }
        });
    }
}
