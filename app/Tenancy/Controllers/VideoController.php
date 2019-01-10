<?php

namespace App\Tenancy\Controllers;

use App\Models\Video;
use App\Models\sCategory;
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
        // $grid->column('thumb','预览图')->display(function($img){
        //     return "<a href='/uploads/{$img}' target='_blank'>" . \imageUrl($img?:'','admin') . "</a>";
        // });
        $grid->column('thumb','预览图')->image('uploads',300,100);
        $grid->column('link','链接')->display(function($v){
            if(strstr($v,'http')){
                return "<a href='{$v}' target='_blank'>点击</a>";
            }
            return "<a href='/uploads/{$v}' target='_blank'>点击</a>";
        });
        $grid->column('user.name','用户');
        
        $grid->status('状态')->editable('select',Video::STATUS_MAP);

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
            return Video::STATIS_MAP[$status] ?:'未知';
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
        $form->text('title','标题')->rules('required|min:6')->help("最少6个字符");
        $form->image('thumb','预览图')->uniqueName()->rules('required|image')->move('video/thumbs');

        $form->display('user.name','用户');
        $form->hidden('user_id')->default(0);
        $form->select('category_id','分类')->options(sCategory::buildSelectOptions())->rules('required');
        $form->file('link','视频文件')->help('请输入视频链接')->uniqueName()->rules('required')->move('video/files');
        $form->switch('status','状态');
        


        return $form;
    }
}
