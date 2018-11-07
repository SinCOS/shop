<?php

namespace App\Admin\Controllers;

use App\Models\Video;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class VideoController extends Controller
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
            ->header('视频列表')
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
            ->header('详细')
            ->description('')
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
        $grid = new Grid(new Video);
        $grid->filter(function($filter){
            $filter->like('title','标题');

        });

        $grid->actions(function($action){
            $action->disableView();
        });
        
        $grid->created_at('上传时间')->sortable();
        $grid->title('标题');
        $grid->column('thumb','预览图')->display(function($img){
            return "<a href='/uploads/{$img}' target='_blank'>" . imageUrl($img?:'','admin') . "</a>";
        });
        $grid->column('link','链接')->link();
        $grid->column('user.name','用户');
        
        $grid->status('状态')->editable('select',['0' =>'待审核','1'=>'审核通过']);

        $grid->disableExport();
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
        $show = new Show(Video::findOrFail($id));

        $show->title('标题');
        $show->image('thumb','预览图');
        $show->field('category','分类')->as(function($cat){
            return $cat->title;
        });
        $
        $show->field('link','链接')->link();
        $show->field('user.name','用户名');
        $show->status('状态')->as(function($status){
            return $status ? '通过':'审核';
        });
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Video);
        $form->text('title','标题')->rules('required|min:6');
        $form->image('thumb','预览图')->uniqueName()->rules('required|image');

        $form->display('user.name','用户');
        $form->select('category_id','分类')->options(Category::videoAll())->rules('required');
        $form->url('link','链接')->help('请输入视频链接')->rules('required');
        $form->switch('status','状态');
        


        return $form;
    }
}
