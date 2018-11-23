<?php

namespace App\Tenancy\Controllers;

use App\Models\sCategory;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class sCategoryController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
	public function index(Content $content) {
		return $content
			->header('文章分类')
			->description('description')
			->row(function (Row $row) {

				// 只能在同一级排序拖动，不允许二级
				$row->column(6, sCategory::tree(function (Tree $tree) {

					// $tree->disableCreate();
				
					$tree->nestable(['maxDepth' => 1])
						->branch(function ($branch) {

							$icon = "<i class='fa {$branch['icon']}'></i>";

							return $icon . " {$branch['id']} " . $branch['title'];
						});
				}));

				// 新建表单
				$row->column(6, function (Column $column) {
					$form = new \Encore\Admin\Widgets\Form();
					$form->action(admin_base_path('scategory'));

					$form->select('parent_id', '上级分类')->options(sCategory::selectOptions());
					$form->text('title', '分类名')->rules('required|unique:categories,title');
					$form->icon('icon', '图标')->default('fa-bars')->rules('required');
					// $form->image('thumb', '缩略图')->uniqueName()->rules('required');

					// $form->hidden('shop_id')->default($shop_id);
					// if (\Admin::user()->isAdministrator()) {
					// 	$form->switch('is_index', '首页显示')->states([
					// 		'on' => ['value' => 1, 'text' => '打开', 'color' => 'success'],
					// 		'off' => ['value' => 0, 'text' => '关闭', 'color' => 'danger'],
					// 	]);
					// 	// $form->select('type', '类型')->options([
					// 	// 	'0' => '正常',
					// 	// 	'1' => '核销',
					// 	// 	'2' => '其他',
					// 	// ]);
					// }

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
        $grid = new Grid(new sCategory);



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
        $show = new Show(sCategory::findOrFail($id));



        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new sCategory);

        $form->text('title', '分类名');
		$form->icon('icon', '图标');
		$form->select('parent_id', '上级分类')->options(
			sCategory::selectOptions());
		// $form->image('thumb', '缩略图');
		$form->hidden('shop_id')->default($shop_id);

		$form->text('description', '描述');

        return $form;
    }
}
