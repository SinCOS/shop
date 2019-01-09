<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class CategoryController extends Controller {
	use HasResourceActions;

	/**
	 * Index interface.
	 *
	 * @param Content $content
	 * @return Content
	 */
	public function index(Content $content) {
		if (\Admin::user()->isAdministrator()) {
			$shop_id = 0;  
		} elseif (\Admin::user()->can('category')) {
            $shop_id = \Admin::user()->shop_id;
		}
		return $content
			->header('商品分类')
			->description('description')
			->row(function (Row $row) use ($shop_id) {

				// 只能在同一级排序拖动，不允许二级
				$row->column(6, Category::tree(function (Tree $tree) use ($shop_id) {

					// $tree->disableCreate();
					$tree->query(function ($model) use ($shop_id) {
						return $model->where('shop_id', $shop_id);
					});
					$tree->nestable(['expandAll'=>true])->branch(function ($branch) {

							$icon = "<i class='fa {$branch['icon']}'></i>";
							//. " {$branch['id']} "
							return $icon  . $branch['title'];
						});
				}));
				\Admin::script('$(function(){$(\'.dd\').nestable(\'collapseAll\');});');
				// 新建表单
				$row->column(6, function (Column $column) use ($shop_id) {
					$form = new \Encore\Admin\Widgets\Form();
					$form->action(admin_base_path('category'));

					$form->select('parent_id', '上级分类')->options(
						Category::selectOptions(function ($model) use ($shop_id) {
							return $model->where('shop_id', $shop_id);
						}));
					$form->text('title', '分类名')->rules('required|unique:categories,title');
					$form->icon('icon', '图标')->default('fa-bars')->rules('required');
					$form->image('thumb', '缩略图')->uniqueName()->rules('required');
					$form->number('order','序号')->rules('min:0');
					$form->hidden('shop_id')->default($shop_id);
					if (\Admin::user()->isAdministrator()) {
						$form->switch('is_index', '首页显示')->states([
							'on' => ['value' => 1, 'text' => '打开', 'color' => 'success'],
							'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
						]);
						$form->select('type', '类型')->options([
							'0' => '正常',
							'1' => '核销',
							'2' => '其他',
							'3' => '全球购',
							'4' => '城市分类'
						]);
					}

					$form->hidden('_token')->default(csrf_token());

					$column->append((new Box('新增', $form))->style('success'));
                    \Admin::script("$('.box-header').children('.pull-right').remove();");
				});
			});
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
			->header('详情')
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
			->description('description')
			->body($this->form()->edit($id));
	}

	/**
	 * Make a show builder.
	 *
	 * @param mixed $id
	 * @return Show
	 */
	protected function detail($id) {

		$show = new Show(Category::findOrFail($id));

		$show->field('id');
		$show->field('title', '分类名');
		$show->field('thumb', '缩略图')->unescape()->as(function ($thumb) {
			return imageUrl($thumb);
		});

		//$form->hidden('shop_id')->default($shop_id);
		if (\Admin::user()->isAdministrator()) {
			$show->field('is_index', '首页显示')->as(function ($index) {
				return index == 1 ? '是' : '否';
			});
		}
		$show->field('description', '描述');
		$show->field('order', '排序');
		$show->field('created_at', '创建时间');
		$show->field('updated_at', '修改时间');

		return $show;
	}

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {

		if (\Admin::user()->isRole('operator')) {
			$shop_id = \Admin::user()->shop_id;

		} elseif (\Admin::user()->isAdministrator()) {
			$shop_id = 0;

		}
		$form = new Form(new Category);

		$form->text('title', '分类名');
		$form->icon('icon', '图标');
		$form->select('parent_id', '上级分类')->options(
			Category::selectOptions(function($query)use($shop_id){
				return $query->where('shop_id',$shop_id);
			}));
		$form->image('thumb', '缩略图')->uniqueName()->rules('required');
		$form->hidden('shop_id')->default($shop_id);
		if (\Admin::user()->isAdministrator()) {

			$form->select('type', '类型')->options([
				'0' => '正常',
				'1' => '核销',
				'2' => '其他',
				'3' => '全球购',
				'4' => '城市分类'
			]);
			$form->switch('is_index', '首页显示')->states([
				'on' => ['value' => 1, 'text' => '打开', 'color' => 'success'],
				'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
			]);
		}
		$form->number('order','序号')->rules('min:0');
		$form->text('description', '描述');

		return $form;
	}

	/**
	 * 分类下有商品，不允许删除
	 *
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function destroy($id) {
		/**
		 * @var $category Category
		 */
		$category = Category::query()->findOrFail($id);

		if ($category->products()->exists()) {
			return response()->json(['status' => false, 'message' => '分类下有商品存在，不允许删除']);
		}

		if ($category->delete()) {
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
