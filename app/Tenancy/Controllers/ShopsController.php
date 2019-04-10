<?php

namespace App\Tenancy\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Shop;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Models\Agent;
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
			->header('商铺列表')
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
		$grid->filter(function ($filter) {
			 $filter->disableIdFilter();
			
			$filter->like('title', '店铺名');
			$filter->like('address', '地址');
			$filter->column(1/2,function($filter){
				$filter->equal('cat_id','分类')->select(Category::selectOptions(null,'首页'));
				$filter->equal('status', '状态')->select(Shop::SHOP_STATUS);
			});
		
			if(\Admin::user()->isAdministrator()){
				$filter->column(1/2,function($filter){
					//\DB::table('district')->where('code',request('province_id',330000)->pluck('name','code'))\DB::table('district')->where('code',request('city_id',330400)
					$filter->equal('province_id','省')->select(\DB::table('district')->where('parent_id',1)->pluck('name','code'))->load('city_id','/api/city')->default(330000);
					$filter->equal('city_id','市')->select()->load('district_id','/api/city')->default(330400);
					$filter->equal('district_id','区')->select();
				});
			}
			
		});
	
		$grid->tools(function ($tools) {
			$tools->batch(function ($batch) {
				$batch->disableDelete();
			});
		});
		$grid->model()->whereNotIn('id',[1]);
		$grid->actions(function($actions){
			//$actions->append('<a href="/shops/{$actions->getKey()}" target="_blank"><i class="fa fa-eye"></i></a>');
		});
		$user = \Admin::user();
		if($user->isRole('agent')){
			
			$id = \Admin::user()->agent->id;
			$grid->model()->where('agent_id', $id);
		}
		if($user->isRole('saller')){
			$id = \Admin::user()->agent->id;
			$grid->model()->where('saller_id', $id);
		}
		$grid->disableExport();
		$grid->disableCreateButton();
		$grid->actions(function ($actions) {
			if($actions->getKey() == 1){
				
			}
			$actions->disableDelete();

			// $actions->disableEdit();
			// $actions->disableView();
		});
		//$grid->disableRowSelector();
		$grid->id('Id')->display(function($id){
			return $id == 1 ? '系统店铺' : $id;
		});
		$grid->column('user.name','管理用户');
		$grid->title('店铺名');
		$grid->logo('Logo')->image('',100,85);
		$grid->column('category.title','商铺分类');
		// $grid->background_image('Background image');
		$grid->status('状态')->display(function($status){
            $msg = Shop::SHOP_STATUS[$status] ?:'未知';
            return "<span class='alert-info'>{$msg}</span>";
        });
		$grid->contcat_people('联系人');
        $grid->concat_phone('联系电话');
		// $grid->address('地址');
		$grid->money('Money');
		// $grid->note('Note');
		$grid->serve_rating('服务评分');
		$grid->speed_rating('速度评分');
		// $grid->area('Area');
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
        $show->user('店家用户名',function($user){
            // $user->setResource('/admin/users');
            $user->panel()
            ->tools(function ($tools) {
                $tools->disableEdit();
                $tools->disableList();
                $tools->disableDelete();
            });
            $user->name('用户名');
            $user->mobile('手机号码');
        });
		$show->panel()
			->tools(function ($tools) {
				//$tools->disableEdit();
				//$tools->disableList();
				$tools->disableDelete();
			});
		$show->id('Id');
		$show->status('店铺状态')->using(Shop::SHOP_STATUS);
		$show->title('店铺名');
		
		$show->field('category','店铺分类')->as(function($cat){
            return $cat->title;
        });
		$show->created_at('创建时间');
		$show->background_image('背景图')->image();

		$show->concat_phone('店铺电话');
		$show->address('店铺地址');
		$show->contcat_people('联系人');
		$show->logo('Logo')->image();
		$show->money('余额');
		$show->note('Note');
		$show->serve_rating('服务评分');
		$show->speed_rating('速度评分');
		$show->agent_id('代理');
		$show->work_id('业务员');
		$show->updated_at('最后更新');

		$show->area('经纬度')->as(function ($areas) {
			// var_dump($areas);
			return $this->area['lat'] . ',' . $this->area['lgt'];
		});
		return $show;
	}
	public function shopAudit(){

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

		$form->display('user_id', '用户')->with(function ($user_id) {
			try {
				$user = \App\Models\User::findOrFail($user_id);
				return $user->name;
			} catch (\Exception $e) {
				return '';
			}

		});
        $form->distpicker([
             'province_id' => '省',
    'city_id'     => '市',
    'district_id' => '区']);
//JDPBZ-UF3HX-DXB4X-7QHYL-PVZ6F-4OBHX

		$form->text('title', '店铺名称')->rules('required|min:6')->help('最少6个字符');
		$form->image('background_image', '背景图片')->uniqueName();
		$form->select('status', '状态')->options(Shop::SHOP_STATUS);
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
		$form->select('agent_id', '代理')->options(\App\Models\Agent::pluck('name','id'))->rules('required');
		// $form->select('work_id', '业务员');
		// if(\Admin::user()->isAdministrator()){
		// 	$form->select('cat_id', '店铺所属分类')->options(Category::selectOptions(function ($query) {
		// 		return $query->where('shop_id', '=', 0);
		// 	}, '请选择'))->rules('required|exists:categories,id');
		// }else{
			$form->select('cat_id', '店铺所属分类')->options(Category::selectOptions(function($query){
				return $query->where('shop_id',0)->where('id','<>',30);
			}));
		// }
		
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
			$form->image('jyxkz','经营许可证')->uniqueName();
		});
		$form->saving(function($form){
			if ($form->status == Shop::SHOP_STATUS_NORMAL && $form->model()->status == Shop::SHOP_STATUS_APPLY) {
				if (!\DB::table('admin_role_users')->where('user_id', $form->user_id)->first()) {
					\DB::table('admin_role_users')->insert([
						'role_id' => 2,
						'user_id' => $form->model()->user_id,
					]);
				}

			}
		});
		$form->saved(function($form){
			$user = $form->model()->user;;
			//dd($user->toJson());
			if($form->status == Shop::SHOP_STATUS_NORMAL  && $user && $user->shop_id == 0){
				$form->model()->user->update(['shop_id' => $form->model()->id, 'password' => bcrypt($user->mobile)]);
	
			}
			
		});
		return $form;
	}
}
