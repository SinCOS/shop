<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ShopsController extends Controller {
	use HasResourceActions;

	/**
	 * Index interface.
	 *
	 * @param Content $content
	 * @return Content
	 */
	public function index(Content $content) {
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
	public function show($id, Content $content) {
		return $content
			->header('店铺信息')
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
	public function edit($id, Content $content) {
		return $content
			->header('编辑店铺')
			->description('请在下面填写完整信息')
			->body($this->form()->edit($id));
	}

	/**
	 * Create interface.
	 *
	 * @param Content $content
	 * @return Content
	 */
	public function create(Content $content) {
		return $content
			->header('开通新店铺')
			->description('请在下面填写完整信息')
			->body($this->form());
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		$grid = new Grid(new Shop);
        $grid->filter(function($filter){
            $filter->equal('status','状态')->select(['0' =>'待审核','1'=>'正在运营','-1' =>'已关闭']);
        });
		$grid->tools(function ($tools) {
			$tools->batch(function ($batch) {
				$batch->disableDelete();
			});
		});
        $grid->disableCreateButton();
		$grid->actions(function ($actions) {
			$actions->disableDelete();

			// $actions->disableEdit();
			// $actions->disableView();
		});
		$grid->id('Id');
		
		// $grid->updated_at('Updated at');
		$grid->user_id('管理用户');
		$grid->title('店铺名');
        $grid->logo('Logo')->display(function($url){
            // return \imageUrl($url);
            return '';
        });
        $grid->cat_id('Cat id');
		// $grid->background_image('Background image');
		$grid->status('状态')->option(['0' => '待审核', '1' => '正常营业', '-1' => '已关闭']);
		$grid->concat_phone('联系电话');
		$grid->address('地址');
		$grid->contcat_people('联系人');
		
		$grid->money('Money');
		// $grid->note('Note');
		$grid->serve_rating('服务评分');
		$grid->speed_rating('速度评分');
		$grid->area('Area');
		// $grid->agent_id('Agent id');
		// $grid->work_id('Work id');
		
        $grid->created_at('创建时间');
		return $grid;
	}

	/**
	 * Make a show builder.
	 *
	 * @param mixed $id
	 * @return Show
	 */
	protected function detail($id) {
		$show = new Show(Shop::findOrFail($id));
		$show->panel()
			->tools(function ($tools) {
				//$tools->disableEdit();
				//$tools->disableList();
				 $tools->disableDelete();
			});
		$show->id('Id');
		$show->status('店铺状态')->using(['0' => '待审核', '1' => '正常营业', '-1' => '已关闭']);
		$show->title('店铺名');
		$show->user_id('用户名')->as(function ($uid) {
			return \App\Models\User::find($uid)->name;
		});
		$show->cat_id('店铺分类');
		$show->created_at('创建时间');
		$show->background_image('背景图')->image();

		$show->concat_phone('店铺电话');
		$show->address('店铺地址');
		$show->contcat_people('联系人');
		$show->logo('Logo');
		$show->money('余额');
		$show->note('Note');
		$show->serve_rating('服务评分');
		$show->speed_rating('速度评分');
		$show->area('Area');
		$show->agent_id('市级代理');
		$show->work_id('业务员');
		$show->updated_at('最后更新');

		return $show;
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {
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
        
        $form->display('user_id', '用户')->with(function($user_id){
                try {
                    $user = \App\Models\User::findOrFail($user_id);
                    return $user->name;
                } catch (\Exception $e) {
                    return '';
                }
                
                
        });
        

	
		$form->text('title', 'Title');
		$form->image('background_image', '背景图片')->uniqueName();
		$form->switch('status', 'Status');
		$form->text('concat_phone', '联系电话');
		$form->text('address', 'Address');
		$form->text('contcat_people', '联系人');
		$form->text('logo', '商铺logo');
		$form->display('money', '金额');
        // if (\Admin::user()->role('administrator')) {
        //     $form->display('note', 'Note');
        // }
		
		$form->rate('serve_rating', '服务评分')->default(5.00);
		$form->rate('speed_rating', '速度评分')->default(5.00);
		$form->text('area', 'Area');
		$form->display('agent_id', '市级代理');
		$form->display('work_id', '业务员');
		$form->display('cat_id', '店铺所属分类')->with(function($cat_id){
            return \App\Models\Category::find($cat_id)->title;
        });

		return $form;
	}
}
