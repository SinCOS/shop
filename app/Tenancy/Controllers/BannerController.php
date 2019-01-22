<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
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
	{	if (\Admin::user()->isRole('agent')) {
		$category_id = \Admin::user()->agent->category_id;
		if($category_id != 0){
			return $content
			->header('活动轮播图')
			->description('description')
			->body('<script>alert("请联系市管理员添加");window.location = "/";</script>');
		}
		$grid->model()->where('city_id', \Admin::user()->city_id);
	}
		return $content
			->header('活动轮播图')
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
			->header('编辑')
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
		$grid->filter(function ($filter) {
			 $filter->disableIdFilter();
			//$filter->like('name', '活动名');
			$arr = [0=>'轮播',1=>'首页店铺',];
		//if(\Admin::user()->isAdministrator())
			$arr[2]='附近轮播';
			$filter->equal('leixing','涮选')->radio($arr);
		});

	
		if (\Admin::user()->isRole('agent')) {
		
			$grid->model()->where('city_id', \Admin::user()->city_id);
		}

		$grid->actions(function($actions){
			$actions->disableView();
		});

		$grid->name('活动名');
		$grid->column('thumb', '轮播图')->image('', 100, 80);
		$grid->column('category.title','所属分类');
		// $grid->order('排序')->editable('text');
		$grid->not_before('开始时间');
		$grid->not_after('结束时间');
		$grid->enabled('是否启用')->switch([
			'on' =>
				['value' => 1, 'text' => '是', 'color' => 'primary'],
			'off' =>
				['value' => 0, 'text' => '否', 'color' => 'default']
		]);
			//display(function ($true) {
			//return $true ? '是' : '否';
	//})
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
		$form->distpicker([
			'province_id' => '省',
			'city_id' => '市',
			'district_id' => '区',
		]);
		$form->select('category_id', '分类')->options(Category::selectOptions(function ($query) {
			return $query->where('parent_id', 0)->where('shop_id', 0);
		}, '首页'));
		$form->text('name', '活动名')->rules('required')->help('必填');
		$form->image('thumb', '图片')->uniqueName()->rules('required')->help('必填');
		// $form->url('link','活动链接');
		$form->radio('leixing','类型')->options([0=>'轮播',1=>'首页店铺','2' =>'附近轮播']);
		$form->radio('leixing2','类型')->options([
			'0' => '店铺',
			'1' => '活动 '
		]);
		$form->text('huodong_id','活动')->help('请输入店铺Id或者活动Id');
		$form->datetimeRange('not_before', 'not_after', '活动时间')->help('必填');

		$form->switch('enabled', '启用')->options(['1' => '是', '0' => '否'])->default(1);
		return $form;
	}
}
