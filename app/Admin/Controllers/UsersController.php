<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
// use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Grid\Filter;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

class UsersController extends Controller
{
    // use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('会员列表')
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
        $grid = new Grid(new User);
        $grid->filter(function($filter){
            $filter->equal('mobile','手机号码')->mobile();
        });
        $grid->actions(function($actions){
            $actions->disableDelete();
        });
        $grid->disableExport();
        $grid->disableCreation();
        $grid->tools(function ($tools) {
    //关闭批量删除
    $tools->batch(function ($batch) {
        $batch->disableDelete();
    });
});
        // 排序最新的
        $grid->model()->latest();

        $grid->column('id', 'Id');
        $grid->column('username','账户名');
        $grid->column('name', '昵称');
        $grid->column('sex', '性别')->display(function ($sex) {
            return User::SEXES[$sex] ?? '未知';
        });
        $grid->column('mobile', '手机');
        $grid->column('avatar', '头像')->image();
        // $grid->column('github_name', 'Github昵称');
        // $grid->column('qq_name', 'QQ昵称');
        // $grid->column('weibo_name', '微博昵称');
        $grid->column('login_count', '登录次数')->sortable();
        $grid->column('status', '是否激活')->display(function ($isActive) {
            return $isActive == User::ACTIVE_STATUS
                ? "<span class='label' style='color: green;'>激活</span>"
                : "<span class='label' style='color: red;'>未激活</span>";
        });
        $grid->column('created_at', '创建时间');
        $grid->column('updated_at', '修改时间');


        // 筛选功能
        $grid->filter(function (Filter $filter) {
           $filter->disableIdFilter();
           $filter->like('name', '用户名');
           $filter->like('email', '邮箱');
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', 'Id');
        $show->field('name', '用户名');
        $show->field('sex', '性别')->as(function ($sex) {
            return User::SEXES[$sex] ?? '未知';
        });
        $show->field('mobile', '手机号码');
        $show->field('avatar', '头像')->as(function ($avatar) {
            return imageUrl($avatar);
        });
        // $show->field('github_name', 'Github昵称');
        $show->field('qq_name', 'QQ昵称');
        $show->field('weibo_name', '微博昵称');
        $show->field('login_count', '登录次数');
        $show->field('is_active', '是否激活')->display(function ($isActive) {
            return $isActive == User::ACTIVE_STATUS
                ? "<span class='label' style='color: green;'>激活</span>"
                : "<span class='label' style='color: red;'>未激活</span>";
        });
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '修改时间');
        $show->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });;
        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // 前台用户注册必须要有这个 token，兼容一下
     return Admin::form(User::class, function (Form $form) {
        // $form = new Form(tap(new User, function ($user) {
        //     $user->active_token = str_random(60);
        // }));

        $form->text('name', '用户名')->rules(function (Form $form) {
            $rules = 'required|unique:users,id';

            // 更新操作
            if (! is_null($id = $form->model()->getKey())) {
                $rules .= ",{$id}";
            }

            return $rules;
        });
        $form->select('sex', '性别')->rules('required|in:0,1')->options(User::SEXES)->default(1);
        $form->display('mobile', '手机号码');
        // ->rules(function (Form $form) {
        //     $rules = 'required|unique:users,mobile|regex:/^1[34578][0-9]{9}$/';

        //     // 更新操作
        //     if (! is_null($id = $form->model()->getKey())) {
        //         $rules .= ",{$id}";
        //     }

        //     return $rules;
        // });
        $form->password('password', '密码');
        $form->image('avatar', '头像')->uniqueName()->move('avatars');

        $form->switch('is_active', '激活');
        $form->saving(function (Form $form) {
        if ($form->password && $form->model()->password != $form->password) {
            $form->password = bcrypt($form->password);
        }
        });

            $form->tools(function (Form\Tools $tools) {

                // Disable `List` btn.
                $tools->disableList();

                // Disable `Delete` btn.
                $tools->disableDelete();

                // Disable `Veiw` btn.
                $tools->disableView();

                // Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
                //$tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
            });
            $form->footer(function ($footer) {

                // disable reset btn
                $footer->disableReset();

                // disable submit btn
                //$footer->disableSubmit();

                // disable `View` checkbox
                $footer->disableViewCheck();

                // disable `Continue editing` checkbox
                $footer->disableEditingCheck();

                // disable `Continue Creating` checkbox
                $footer->disableCreatingCheck();
            });
        return $form;
    });
 }
}
