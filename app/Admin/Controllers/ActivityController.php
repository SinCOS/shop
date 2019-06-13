<?php

namespace App\Admin\Controllers;

use App\Models\Activity;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Models\Shop;
class ActivityController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('活动管理')
            ->description('活动列表')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('新增')
            // ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Activity);
        if(\Admin::user()->isRole('agent')){
           $agent = \Admin::user()->agent;
          // $grid->model()->where('city_id',   $agent->city_id);
        }
        $grid->id('ID');
        $grid->title('活动名称');
        
        $grid->created_at('日期')->sortable();
        //$grid->updated_at('Updated at');
        $grid->actions(function($actions){
            $actions->disableView();
    
        });
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->disableFilter();
        return $grid;
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Activity);

        $form->tools(function (Form\Tools $tools) {

            // 去掉`列表`按钮
            $tools->disableList();
        
            // 去掉`删除`按钮
            $tools->disableDelete();
        
            // 去掉`查看`按钮
            $tools->disableView();
        
            // 添加一个按钮, 参数可以是字符串, 或者实现了Renderable或Htmlable接口的对象实例
          // $tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
        });
        // $form->display('Created at');
        // $form->display('Updated at');
        $form->text('title','标题')->rules('required|min:3')->help('必填,不少于3个字符');
        $form->image('headthumb','头部图片')->uniqueName()->rules('required')->move('activity');
        // $form->listbox('permissions', trans('admin.permissions'))->options(Shop::all()->pluck('title', 'id'));
        
        $form->hasMany('param','参加的店铺',function($form){
            $form->image('url','图片')->uniqueName();
            $form->select('shop_id','店铺')->options(\App\Models\Shop::all()->pluck('title','id'))->rules('required');
        });
        $form->footer(function ($footer) {

            // 去掉`重置`按钮
            $footer->disableReset();
        
            // 去掉`提交`按钮
            // $footer->disableSubmit();
        
            // 去掉`查看`checkbox
            $footer->disableViewCheck();
        
            // 去掉`继续编辑`checkbox
            $footer->disableEditingCheck();
        
            // 去掉`继续创建`checkbox
            $footer->disableCreatingCheck();
        
        });
        return $form;
    }
}