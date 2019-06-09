<?php

namespace App\Tenancy\Controllers;

use App\Models\Tixian;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use App\Exceptions\InvalidRequestException;
class TiXianController extends Controller
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
            ->header('账户提现')
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
            ->header('详情')
            ->description('提现细节')
            ->body(view('tenancy.tixian.show',['detail' => Tixian::findOrFail($id)]));
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
    protected function _withdraw($tixian){
        
    }
    protected function withdraw(Tixian $tixian){
        if($tixian->status !== Tixian::STATUS_APPLIED){
            throw new InvalidRequestException('提现状态不正确');
        }
        if (request()->input('agree')) {
            // 调用退款逻辑
            $tixian->update([
                'status' => 1,
            ]);
            //$this->_refundOrder($order);
        } else {
            // 将拒绝退款理由放到订单的 extra 字段中
            $tixian->user->increment('money',$tixian->number);
            $tixian->update(['status' => 2]);
          
        }

        return $tixian;
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
        $grid = new Grid(new Tixian);
        $grid->filter(function($filter){
            $filter->equal('status','打款状态')->select(['0' =>'申请','1'=>'打款成功','2'=>'失败']);
        });
        $grid->tools(function ($tools) {
			$tools->batch(function ($batch) {
				$batch->disableDelete();
			});
        });
        $grid->model()->orderBy('created_at','desc');
        $grid->actions(function($actions){
            $actions->disableEdit();
            $actions->disableDelete();
        });
        $grid->disableCreation();
        $grid->disableExport();
        $grid->column('user.name','账户');
        $grid->column('user.mobile','手机');
        $grid->number('金额');
        $grid->created_at('申请时间')->sortable();
        $grid->status('状态')->display(function($v){
            $arr = ['申请','成功','失败'];
            return $arr[$v];
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
        $show = new Show(Tixian::findOrFail($id));
        $show->panel()
    ->tools(function ($tools) {
        $tools->disableEdit();
        $tools->disableList();
        $tools->disableDelete();
    });;
        $show->bankinfo('姓名')->as(function($v){
           
            return  "<p>{$v['name']}</p><br><p>{$v['bankname']}</p>";
        });
        $show->column('bankinfo','银行')->as(function($v){
            var_dump($this->bankinfo);
            return $v['bankname'];
        });
        $show->column('bankinfo','银行号')->as(function($v){
            return $v['banknumber'];
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
        $form = new Form(new Tixian);

       //$form->html()

        return $form;
    }
}
