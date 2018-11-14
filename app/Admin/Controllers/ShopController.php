<?php

namespace App\Admin\Controllers;

use App\Models\Category;
use App\Models\Shop;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ShopController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    // public function index(Content $content)
    // {
    //     return $content
    //         ->header('Index')
    //         ->description('description')
    //         ->body($this->grid());
    // }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    // public function show($id, Content $content)
    // {
    //     return $content
    //         ->header('Detail')
    //         ->description('description')
    //         ->body($this->detail($id));
    // }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit(Content $content)
    {   
        $id =Shop::where('user_id','=',\Admin::user()->id)->value('id');
        // return $id;
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
        $grid = new Grid(new Shop);

        $grid->id('Id');
        $grid->created_at('Created at');
        $grid->updated_at('Updated at');
        $grid->user_id('User id');
        $grid->title('Title');
        $grid->background_image('Background image');
        $grid->status('Status');
        $grid->concat_phone('Concat phone');
        $grid->address('Address');
        $grid->contcat_people('Contcat people');
        $grid->logo('Logo');
        $grid->money('Money');
        $grid->note('Note');
        $grid->serve_rating('Serve rating');
        $grid->speed_rating('Speed rating');
        $grid->area('Area');
        $grid->agent_id('Agent id');
        $grid->work_id('Work id');
        $grid->type('Type');
        $grid->thumb('Thumb');
        $grid->province_id('Province id');
        $grid->city_id('City id');
        $grid->district_id('District id');

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
        $show = new Show(Shop::findOrFail($id));

        $show->id('Id');
        $show->created_at('Created at');
        $show->updated_at('Updated at');
        $show->user_id('User id');
        $show->title('Title');
        $show->background_image('Background image');
        $show->status('Status');
        $show->concat_phone('Concat phone');
        $show->address('Address');
        $show->contcat_people('Contcat people');
        $show->logo('Logo');
        $show->money('Money');
        $show->note('Note');
        $show->serve_rating('Serve rating');
        $show->speed_rating('Speed rating');
        $show->area('Area');
        $show->agent_id('Agent id');
        $show->work_id('Work id');
        $show->type('Type');
        $show->thumb('Thumb');
        $show->province_id('Province id');
        $show->city_id('City id');
        $show->district_id('District id');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Shop);

        $form->tools(function (Form\Tools $tools) {

            // // Disable `List` btn.
            // $tools->disableList();

            // Disable `Delete` btn.
            $tools->disableDelete();

            // // Disable `Veiw` btn.
            // $tools->disableView();

            // // Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
            // $tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
        });

        $form->display('user_id', '用户')->with(function ($user_id) {
            try {
                $user = \App\Models\User::findOrFail($user_id);
                return $user->name;
            } catch (\Exception $e) {
                return '';
            }

        });
    //     $form->distpicker([
    //          'province_id' => '省',
    // 'city_id'     => '市',
    // 'district_id' => '区']);
//JDPBZ-UF3HX-DXB4X-7QHYL-PVZ6F-4OBHX

        $form->text('title', '店铺名称');
        $form->image('background_image', '背景图片')->uniqueName()->move('shops/')->help('尺寸750x200');
        $form->select('status', '状态')->options(['0' => '待审核', '1' => '正常营业', '-1' => '已关闭']);
        $form->text('concat_phone', '联系电话');
        $form->text('address', 'Address');
        $form->text('contcat_people', '联系人');
        $form->image('logo', '商铺logo')->uniqueName();
        $form->display('money', '金额');
        // if (\Admin::user()->role('administrator')) {
        //     $form->display('note', 'Note');
        // }

        $form->rate('serve_rating', '服务评分')->default(5.00);
        $form->rate('speed_rating', '速度评分')->default(5.00);
        $form->display('agent_id', '市级代理');
        $form->display('work_id', '业务员');
        $form->select('cat_id', '店铺所属分类')->options(Category::selectOrderAll())->rules('required|exists:categories,id')->readOnly();
        // $form->display('cat_id', '店铺所属分类')->with(function($cat_id){
        //           return \App\Models\Category::find($cat_id)->title;
        //       });
        $form->embeds('area', '地理信息', function ($form) {
            $form->diymap('lat', 'lgt', '经纬度');
            //    $form->text('abc','123');
            // //    $form->saving(function($form){
            // //     $form->model()->lat = request()->get('abc');
            // // });

        });
        $form->display('type', '商户类型')->with(function ($val) {
            return Shop::SHOP_USER_TYPE_MAP[$val] ?: '未知';
        });
        $form->embeds('thumb', '照片', function ($form) {
            $form->image('sfzz', '身份证正面照')->uniqueName();
            $form->image('sfzf', '身份证反面照')->uniqueName();
            $form->image('yyzz', '营业执照正面照')->uniqueName();
        });
        return $form;

      
    }
}
