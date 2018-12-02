<?php

namespace App\Admin\Controllers;

use App\Models\Banner;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class BannerController extends Controller
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
            ->header('Index')
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
        $grid = new Grid(new Banner);
        $user = \Admin::user();
        if($user->iscan('bannber')){
            $grid->model()->where('city_id',$user->agent()->city_id);
        }
        $grid->name('活动名');
        $grid->column('thumb','轮播图')->display(function($img){
            return imageUrl($img?:'','admin');
        });
        // $grid->order('排序')->editable('text');
        $grid->not_before('开始时间');
        $grid->not_after('结束时间');
        $grid->enabled('是否启用')->display(function($true){
            return $true ? '是' : '否' ;
        });

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
        $show = new Show(Banner::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Banner);
            $form->text('name','活动名')->rules('required')->help('必填');
            $form->image('thumb','图片')->uniqueName()->rules('required')->help('必填');
            // $form->url('link','活动链接');

            $form->datetimeRange('not_before','not_after', '活动时间')->help('必填');
    
            $form->radio('enabled', '启用')->options(['1' => '是', '0' => '否'])->default(1);
        return $form;
    }
}
