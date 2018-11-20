<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

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
	public function create(Content $content) {
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
	protected function grid() {
		$grid = new Grid(new Agent);
		$grid->column("name", '代理名');
		$grid->column("user.username", '登录账户');
		$grid->column('user.mobile', '手机号码');
		$grid->column('agent_type', '代理类型')->display(function ($v) {
			return $v == 'agent' ? '代理' : '业务员';
		});
		$grid->column('city_id', '代理区域')->display(function () {

		});

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
		// $footer->disableReset();

		// 去掉`提交`按钮
		// $footer->disableSubmit();

		// // 去掉`查看`checkbox
		// $footer->disableViewCheck();

		// // 去掉`继续编辑`checkbox
		// $footer->disableEditingCheck();

		// // 去掉`继续创建`checkbox
		// $footer->disableCreatingCheck();
		$form->text('name', '代理用户');
		$form->select('user_id', '登录账号')->options(
			\App\Models\User::get()->pluck('mobile', 'id')
		);

		$form->select('agent_type', '代理类型')->options([
			'agent' => '代理',
			'salesman' => '业务员',
		]);
		$form->rate('rate', '利率');
		$form->distpicker([
			'province_id' => '省',
			'city_id' => '市',
			'district_id' => '区',
		], '区域');
		$form->switch('user.is_agent', '激活状态')->options([
			'0' => '未激活',
			'1' => '激活',
		]);
		$form->saved(function ($form) {

			if ($form->user['is_agent'] == 'on') {

				if (!\DB::table('admin_role_users')->where('user_id', $form->user_id)->first()) {
					\DB::table('admin_role_users')->insert([
						'role_id' => 3,
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
		$agent = Agent::find($id);
		if ($this->form()->destroy($id)) {
			\App\Models\User::find($agent->user_id)->update(['is_agent' => 0]);
			\DB::table('admin_role_users')->where('user_id', $agent->user_id)->delete();
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
