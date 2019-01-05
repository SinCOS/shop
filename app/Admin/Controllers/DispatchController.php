<?php

namespace App\Admin\Controllers;

use App\Models\Dispatch;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class DispatchController extends Controller
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
            ->header('配送方案')
            ->description('description')
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
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Dispatch);
        $shop_id = \Admin::user()->shop_id;
        $grid->model()->where('shop_id',$shop_id);
        // $grid->tools(function($tools){
        //     $tools->disableView();
        // });
        $grid->disableExport();
        $grid->disableFilter();
        $grid->disableTools();
        // $grid->disableView();
        $grid->actions(function($actions){  
            $actions->disableView();          
        });
        $grid->name('方案名');
        $grid->startup_price('起送价');
        $grid->price('配送费');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Dispatch::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $shop_id = \Admin::user()->shop_id;
        $form = new Form(new Dispatch);
        $form->text('name','配送方案名')->rules('required');
        $form->decimal('startup_price','起送价');
        $form->decimal('price','配送费');
         $form->gdmap('positions', '配送区域');
        $form->hidden('shop_id')->default( $shop_id);
        $form->saving(function($form)use($shop_id){
            if($form->shop_id == 0){
                $form->shop_id = $shop_id;
            }
            //$form->positions = \DB::raw("MultiPointFromText('MULTIPOINT(({$form->positions}))')");
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
