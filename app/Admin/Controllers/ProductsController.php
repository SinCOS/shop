<?php

namespace App\Admin\Controllers;

use App\Models\Product;
use App\Models\Category;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductsController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('商品列表');
            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑商品');
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('创建商品');
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->id('ID')->sortable();
            $grid->column('category.title','分类');
            $grid->title('商品名称');
            $grid->image('首图')->display(function($img){
                return imageUrl($img,'admin');
            });
            $grid->on_sale('已上架')->display(function ($value) {
                return $value ? '<mark>上架</mark>' : '<mark style="color: red">下架</mark>';
            });
            $grid->price('价格');
            $grid->rating('评分');
            $grid->sold_count('销量')->sortable();
            $grid->review_count('评论数');

            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableDelete();
            });
            $grid->tools(function ($tools) {
                // 禁用批量删除按钮
                $tools->batch(function ($batch) {
                    $batch->disableDelete();
                });
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

        // 创建一个表单
        return Admin::form(Product::class, function (Form $form) {
            $form->tab('商品信息',function($form){
            // 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
            $form->select('category_id','分类')->options(Category::selectOptions(null,'请选择'));
            $form->text('title', '商品名称')->rules('required');
            // 创建一个选择图片的框
            $form->image('image', '封面图片')->rules('required|image')->uniqueName();
            $form->hidden('shop_id')->default('1');
            $form->multipleImage('thumb','副图')->removable()->uniqueName()->options([ 'fileActionSettings' => [
        'showZoom' => true,

    ],'showPreview' => true,'autoReplace'=> false]);

            $form->hasMany('images','主图',function(Form\NestedForm $form){
                $form->image('img','图片')->uniqueName()->move('products/lists/' .date('Y-m-d'));
            });
            // 创建一个富文本编辑器
            // $form->editor('description', '商品描述')->rules('required');
           $form->number('max_buy','用户单次最多购买')->min(0)->default(0)->rules('required|integer|min:0')->help('0 为不限制');
           
           $form->radio('dispatchtype','运费设置')->options(['1' =>'统一邮费','0' => 
            '模板'])->default(1)->help('二选一');
           $form->currency('dispatchprice','统一邮费')->symbol('￥');
           $form->select('dispatchid','邮费模板')->options([]);
            // 创建一组单选框
            $form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default('0');

        })->tab('商品描述',function($form){
              $form->editor('body', '商品描述')->rules('required');
        })->tab('规格',function($form){
            // 直接添加一对多的关联模型
            //$('.iCheck-helper').click(function(){alert($('input[name="is_sku"]:checked').val());});
            $form->radio('is_sku','是否多规格')->options(['0'=>'否','1'=>'是'])->default(0);
            $form->hasMany('skus', function (Form\NestedForm $form) {
                $form->text('title', 'SKU 名称')->rules('required');
                $form->text('description', 'SKU 描述')->rules('required');
                $form->currency('price', '单价')->symbol('￥')->rules('required|numeric|min:0.01');
                $form->currency('cost', '市场价')->symbol('￥')->rules('required|numeric|min:0.01');
                $form->currency('price_on_app','平台价')->symbol('￥')->rules('required|numeric|min:0.01');
                $form->number('stock', '剩余库存')->min(0)->rules('required|integer|min:0');
                $form->number('weight','重量')->min(0)->rules('required|integer|min:0')->help('单位g');
            });
    
        })->tab('营销设置',function($form){

        });
                // 定义事件回调，当模型即将保存时会触发这个回调
            $form->saving(function (Form $form) {
                $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
            });
    });
    }

}
