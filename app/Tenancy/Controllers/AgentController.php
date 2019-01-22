<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Models\Category;
class AgentController extends Controller {
	use HasResourceActions;

	/**
	 * Index interface.
	 *
	 * @param Content $content
	 * @return Content
	 */
	public function index(Content $content) {
		return $content
			->header('代理列表')
			// ->description('description')
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
	public function edit($id, Content $content) {
		return $content
			->header('编辑')
			// ->description('description')
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
			->header('创建')
			// ->description('description')
			->body($this->form());
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */

	protected function grid() {
		$grid = new Grid(new Agent);
		if(\Admin::user()->isRole('agent')){
			//$grid->model()->where('district_id');
		}
			$grid->filter(function ($filter) {
			 $filter->disableIdFilter();
			
	
		
			if(\Admin::user()->isAdministrator()){
				$filter->column(1/2,function($filter){
					//\DB::table('district')->where('code',request('province_id',330000)->pluck('name','code'))\DB::table('district')->where('code',request('city_id',330400)
					$filter->equal('province_id','省')->select(\DB::table('district')->where('parent_id',0)->pluck('name','code'))->load('city_id','/api/city')->default(330000);
					$filter->equal('city_id','市')->select()->load('district_id','/api/city')->default(330400);
					$filter->equal('district_id','区')->select();
				});
			}
			
		});
		$grid->actions(function($actions){
			$actions->disableView();
		});
		$grid->column("name", '代理名');
		$grid->column("user.name", '登录账户');
		$grid->column('category.title','代理分类');
		$grid->column('user.mobile', '手机号码');
		$grid->column('agent_type', '代理类型')->display(function ($v) {
			return $v == 'agent' ? '代理' : '业务员';
		});
	
		// $grid->column('city_id', '代理区域')->display(function () {

		// });

		return $grid;
	}

	/**
	 * Make a show builder.
	 *
	 * @param mixed $id
	 * @return Show
	 */
	protected function detail($id) {
		$show = new Show(Agent::findOrFail($id));

		return $show;
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */

	protected function form() {

		$form = new Form(new Agent);
	
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
		// 去掉`提交`按钮
		// $footer->disableSubmit();
		$form->footer(function($footer){
			// 去掉`查看`checkbox
			$footer->disableViewCheck();
				// $footer->disableReset();
		// 去掉`继续编辑`checkbox
			$footer->disableEditingCheck();
			// // 去掉`继续创建`checkbox
		 $footer->disableCreatingCheck();
		});
		

		
		$form->text('name', '代理用户')->rules('required|min:3')->help('最少三个字符');
		$form->select('user_id', '登录账号')->options(
			\App\Models\User::canAgents()
		)->help('必填，已存在用户的手机号码')->rules(function($form){
			if(!$id = $form->model()->id) {
				
			}
		});
		$form->textarea('description','描述或者备注')->default('');
		$form->select('agent_type', '代理类型')->options([
			'agent' => '代理',
			'salesman' => '业务员',
		])->default('agent');
		$form->rate('rate', '利率')->default(0);
		$form->distpicker([
			'province_id' => '省',
			'city_id' => '市',
			'district_id' => '区',
		], '区域');
		$form->select('category_id','代理分类')->options(Category::selectOptions(function($query){
			return $query->where('parent_id',0);
		},'首页'));
		$form->embeds('param', '照片', function ($form) {
			$form->image('sfzz', '身份证正面照')->uniqueName()->move('agents/');
			$form->image('sfzf', '身份证反面照')->uniqueName()->move('agents/');
			$form->image('yyzz', '营业执照正面照')->uniqueName()->move('agents/');
		});
		$form->switch('user.is_agent', '激活状态')->options([
			'0' => '未激活',
			'1' => '激活',
		]);
		$form->saved(function ($form) {

			if ($form->user['is_agent'] == 'on') {

				if (!\DB::table('admin_role_users')->where('user_id', $form->user_id)->first()) {
					\DB::table('admin_role_users')->insert([
						'role_id' => $form->agent_type =='agent' ? 3 : 4,
						'user_id' => $form->user_id,
					]);
				}

			} else {
				\DB::table('admin_role_users')->where('user_id', $form->user_id)->delete();
			}

		});
		return $form;

	}

	public function destroy($id) {
		if ($this->form()->destroy($id)) {
		
			$data = [
				'status' => true,
				'message' => trans('admin.delete_succeeded'),
			];

		} else {
			$data = [
				'status' => false,
				'message' => trans('admin.delete_failed'),
			];
		}

		return response()->json($data);
	}
}
