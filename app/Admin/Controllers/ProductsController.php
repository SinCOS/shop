<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ProductsController extends Controller {
	use ModelForm;

	/**
	 * Index interface.
	 *
	 * @return Content
	 */
	public function index() {
		return Admin::content(function (Content $content) {
			$content->header('商品列表');
			$content->body($this->grid());
		});
	}

	/**
	 * Edit interface.
	 *
	 * @param $id
	 * @return Content
	 */
	public function edit($id) {
       
		return Admin::content(function (Content $content) use ($id) {
			$content->header('编辑商品');
			$content->body($this->form()->edit($id));
		});
	}

	/**
	 * Create interface.
	 *
	 * @return Content
	 */
	public function create() {

		return Admin::content(function (Content $content) {
			$content->header('创建商品');
			$content->body($this->form());
		});
	}

	/**
	 * Make a grid builder.
	 *
	 * @return Grid
	 */
	protected function grid() {
		return Admin::grid(Product::class, function (Grid $grid) {
			
            $grid->model()->where('shop_id','=',\Admin::user()->shop_id);
			$grid->product_ssn('产品SSN')->sortable();

            $grid->column('category.title', '分类');
			$grid->title('商品名称');
			$grid->image('首图')->image('/uploads', 50, 50);
			$grid->on_sale('已上架')->display(function ($value) {
				return $value ? '<mark>上架</mark>' : '<mark style="color: red">下架</mark>';
			});
			$grid->price('价格');
			$grid->rating('评分');
			$grid->sold_count('销量')->sortable();
			$grid->review_count('评论数');
            $grid->is_verify('是否核销')->display(function($v){
                return $v ? '「是」' : '否';
            });
			$grid->actions(function ($actions) {
				$actions->disableView();
				$actions->disableDelete();
			});
			$grid->tools(function ($tools) {
				// 禁用批量删除按钮
				$tools->batch(function ($batch) {
					$batch->disableDelete();
				});
			});
		});
	}

    public function generHtml($id){
        $allspecs  = \App\Models\ProductSpec::where('product_id',$id)->get()->toArray();

        foreach ($allspecs as $key => $v ) {
            $allspecs[$key]['items'] = \App\Models\ProductSpecItem::where('spec_id',$v['id'])->get()->toArray();
           // $s['items'] = \DB::table('product_spec_item')->where('spec_id',$s['id'])->select();
        }
        
       // / unset($s);
     
        // $piclist1 = unserialize($item['thumb_url']);
        // $piclist  = array();
        // if (is_array($piclist1)) {
        //     foreach ($piclist1 as $p) {
        //         $piclist[] = is_array($p) ? $p['attachment'] : $p;
        //     }
        // }
        $html    = "";
        $options = \App\Models\ProductSku::where('product_id',$id)->orderBy('id','asc')->get()->toArray();
       // var_dump($options);
        $specs   = array();
        if (count($options) > 0) {
            $specitemids = explode("_", $options[0]['specs']);
            foreach ($specitemids as $itemid) {
                foreach ($allspecs as $ss) {
                    $items = $ss['items'];
                    foreach ($items as $it) {
                        if ($it['id'] == $itemid) {
                            $specs[] = $ss;
                            break;
                        }
                    }
                }
            }
            $html = '';
            $html .= '<table class="table table-bordered table-condensed">';
            $html .= '<thead>';
            $html .= '<tr class="active">';
            $len      = count($specs);
            $newlen   = 1;
            $h        = array();
            $rowspans = array();
            for ($i = 0; $i < $len; $i++) {
                $html .= "<th style='width:80px;'>" . $specs[$i]['title'] . "</th>";
                $itemlen = count($specs[$i]['items']);
                if ($itemlen <= 0) {
                    $itemlen = 1;
                }
                $newlen *= $itemlen;
                $h = array();
                for ($j = 0; $j < $newlen; $j++) {
                    $h[$i][$j] = array();
                }
                $l            = count($specs[$i]['items']);
                $rowspans[$i] = 1;
                for ($j = $i + 1; $j < $len; $j++) {
                    $rowspans[$i] *= count($specs[$j]['items']);
                }
            }
          
                $html .= '<th class="info" style="width:130px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
                $html .= '<th class="success" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
                $html .= '<th class="warning" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
                $html .= '<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
                $html .= '<th class="primary" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
                $html .= '<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_productsn_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
                $html .= '<th class="info" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"  VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
                $html .= '</tr></thead>';
            
            for ($m = 0; $m < $len; $m++) {
                $k   = 0;
                $kid = 0;
                $n   = 0;
                for ($j = 0; $j < $newlen; $j++) {
                    $rowspan = $rowspans[$m];
                    if ($j % $rowspan == 0) {
                        $h[$m][$j] = array(
                            "html" => "<td rowspan='" . $rowspan . "'>" . $specs[$m]['items'][$kid]['title'] . "</td>",
                            "id" => $specs[$m]['items'][$kid]['id']
                        );
                    } else {
                        $h[$m][$j] = array(
                            "html" => "",
                            "id" => $specs[$m]['items'][$kid]['id']
                        );
                    }
                    $n++;
                    if ($n == $rowspan) {
                        $kid++;
                        if ($kid > count($specs[$m]['items']) - 1) {
                            $kid = 0;
                        }
                        $n = 0;
                    }
                }
            }
            $hh = "";
            for ($i = 0; $i < $newlen; $i++) {
                $hh .= "<tr>";
                $ids = array();
                for ($j = 0; $j < $len; $j++) {
                    $hh .= $h[$j][$i]['html'];
                    $ids[] = $h[$j][$i]['id'];
                }
                $ids = implode("_", $ids);
                $val = array(
                    "id" => "",
                    "title" => "",
                    "stock" => "",
                    "costprice" => "",
                    "productprice" => "",
                    "marketprice" => "",
                    "weight" => "",
                    'virtual' => ''
                );
                foreach ($options as $o) {
                    if ($ids === $o['specs']) {
                        $val = array(
                            "id" => $o['id'],
                            "title" => $o['title'],
                            "stock" => $o['stock'],
                            "costprice" => $o['cost'],
                            "productprice" => $o['price'],
                            "marketprice" => $o['price_on_app'],
                            "goodssn" => $o['product_sn'],
                            "productsn" => $o['product_sn'],
                            "weight" => $o['weight'],
                            'virtual' => 1
                        );
                        break;
                    }
                }
                    $hh .= '<td class="info">';
                    $hh .= '<input name="option_stock_' . $ids . '[]"  type="text" class="form-control option_stock option_stock_' . $ids . '" value="' . $val['stock'] . '"/>';
                    $hh .= '<input name="option_id_' . $ids . '[]"  type="hidden" class="form-control option_id option_id_' . $ids . '" value="' . $val['id'] . '"/>';
                    $hh .= '<input name="option_ids[]"  type="hidden" class="form-control option_ids option_ids_' . $ids . '" value="' . $ids . '"/>';
                    $hh .= '<input name="option_title_' . $ids . '[]"  type="hidden" class="form-control option_title option_title_' . $ids . '" value="' . $val['title'] . '"/>';
                    $hh .= '<input name="option_virtual_' . $ids . '[]"  type="hidden" class="form-control option_title option_virtual_' . $ids . '" value="' . $val['virtual'] . '"/>';
                    $hh .= '</td>';
                    $hh .= '<td class="success"><input name="option_marketprice_' . $ids . '[]" type="text" class="form-control option_marketprice option_marketprice_' . $ids . '" value="' . $val['marketprice'] . '"/></td>';
                    $hh .= '<td class="warning"><input name="option_productprice_' . $ids . '[]" type="text" class="form-control option_productprice option_productprice_' . $ids . '" " value="' . $val['productprice'] . '"/></td>';
                    $hh .= '<td class="danger"><input name="option_costprice_' . $ids . '[]" type="text" class="form-control option_costprice option_costprice_' . $ids . '" " value="' . $val['costprice'] . '"/></td>';
                    $hh .= '<td class="primary"><input name="option_goodssn_' . $ids . '[]" type="text" class="form-control option_goodssn option_goodssn_' . $ids . '" " value="' . $val['goodssn'] . '"/></td>';
                    $hh .= '<td class="danger"><input name="option_productsn_' . $ids . '[]" type="text" class="form-control option_productsn option_productsn_' . $ids . '" " value="' . $val['productsn'] . '"/></td>';
                    $hh .= '<td class="info"><input name="option_weight_' . $ids . '[]" type="text" class="form-control option_weight option_weight_' . $ids . '" " value="' . $val['weight'] . '"/></td>';
                    $hh .= '</tr>';
              
            }
            $html .= $hh;
            $html .= "</table>";
        }
       
        return [view('admin.tpl.full')->with(['allspecs' => $allspecs])->render(),$html];
    }


	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {

		// 创建一个表单
		return Admin::form(Product::class, function (Form $form) {
            $form->tools(function (Form\Tools $tools) {

            // // Disable `List` btn.
            // $tools->disableList();

            // // Disable `Delete` btn.
            // $tools->disableDelete();

            // // Disable `Veiw` btn.
            $tools->disableView();

            // // Add a button, the argument can be a string, or an instance of the object that implements the Renderable or Htmlable interface
            // $tools->add('<a class="btn btn-sm btn-danger"><i class="fa fa-trash"></i>&nbsp;&nbsp;delete</a>');
        });


            $shop_id = \Admin::user()->shop_id;
			$form->tab('商品信息', function ($form) use($shop_id){
				// 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
           
				$form->select('category_id', '分类')->options(Category::selectOptions(function($query)use($shop_id){
                    return $query->where('shop_id',$shop_id);
                }, '请选择'));
                $form->text('product_ssn','产品SSN')->rules('required');
				$form->text('title', '商品名称')->rules('required');
				// 创建一个选择图片的框
				$form->image('image', '封面图片')->rules('required|image')->uniqueName()->move('products/covers/' . date('Y-m-d'));
				$form->hidden('shop_id')->default($shop_id);
				$form->multipleImage('thumb', '副图')->addElementClass('file_upload')->removable()->uniqueName()->options([
					'fileActionSettings' => [
						'showZoom' => true,
					],
					'uploadAsync' => false,
					'overwriteInitial' => false,
					'showPreview' => true,
					'dropZoneEnabled' => true,
					'autoReplace' => false,
					'showUpload' => false,
					'uploadUrl' => route('upload.file-input'),
                    // 'initialPreviewAsData' => true,
					'uploadExtraData' => [
						'_token' => csrf_token(),
						'_method' => 'POST',
					],
					'layoutTemplates' => [
						'actionUpload' => '',
					],
					'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
				])->help('图片大小 5M 以内，尺寸')->move('products/lists/' . date('Y-m-d'));
                    //Admin::js('/vendor/laravel-admin/bootstrap-fileinput/js/plugins/sortable.min.js');
                $form->number('stock','库存')->min(0)->rules('required|integer|min:0');
                $form->currency('price','销售价');
                $form->hidden('price_on_app')->default(0.00);
				$form->number('max_buy', '用户单次最多购买')->min(0)->default(0)->rules('required|integer|min:0')->help('0 为不限制');

				$form->radio('dispatchtype', '运费设置')->options(['1' => '统一邮费', '0' =>
					'模板'])->default(1)->help('二选一');
				$form->currency('dispatchprice', '统一邮费')->symbol('￥');
				$form->select('dispatchid', '邮费模板')->options([])->default(0);
				// 创建一组单选框
				$form->radio('on_sale', '上架')->options(['1' => '是', '0' => '否'])->default('0');

			})->tab('商品描述', function ($form) {
				$form->editor('body', '商品描述')->rules('required');
			})->tab('规格', function ($form) {
				// 直接添加一对多的关联模型
				//$('.iCheck-helper').click(function(){alert($('input[name="is_sku"]:checked').val());});
				$form->radio('has_sku','是否多规格')->options(['0'=>'否','1'=>'是'])->default(0);

$table = '<div id="specs">';

$table2=<<<TABLE
</div>
<table class="table" id='specTable' style='display:none;'>
        <tbody><tr>
            <td>
                <h4><a href="javascript:;" class="btn btn-primary" id="add-spec" onclick="addSpec();" style="margin-top:10px;margin-bottom:10px;" title="添加规格"><i class="fa fa-plus"></i> 添加规格</a> 
                <a href="javascript:;" onclick="calc()" title="刷新规格项目表" class="btn btn-primary"><i class="fa fa-refresh"></i> 刷新规格项目表</a></h4>
            </td>
        </tr>
    </tbody></table>
    <div class="panel-body table-responsive" id="options" style="padding:0;">
TABLE;
    $product_id = request()->route()->parameter('id');

$cc = $this->generHtml($product_id);

$table0 = $cc[0] ?:'';
$table .= $table0 ?:'';
$table .=$table2;
$tableScript = $cc[1];

$form->html($table . $tableScript . '</div><script src="/vendor/product.js"> </script>');
    // $form->html($tableScript);
    
             
// $con = <<<TEST
// <div class="panel panel-default spec_item" id="spec_Ebw31844lN4qo0iIq0iwIob421nI1Xo4" style="">
//          <div class="panel-body">
//     <input name="spec_id[]" type="hidden" class="form-control spec_id" value="Ebw31844lN4qo0iIq0iwIob421nI1Xo4">
//     <div class="form-group">
//         <label class="col-xs-12 col-sm-3 col-md-2 control-label">规格名</label>
//     <div class="col-sm-9 col-xs-12">
         
//             <input name="spec_title[Ebw31844lN4qo0iIq0iwIob421nI1Xo4]" type="text" class="form-control  spec_title" value="" placeholder="(比如: 颜色)">
                      
//         </div>
//     </div>
//     <div class="form-group">
//         <label class="col-xs-12 col-sm-3 col-md-2 control-label">规格项</label>
//     <div class="col-sm-9 col-xs-12">
//             <div id="spec_item_Ebw31844lN4qo0iIq0iwIob421nI1Xo4" class="spec_item_items">
//                         <div class="spec_item_item" style="float:left;margin:0 5px 10px 0;width:250px;">
//     <input type="hidden" class="form-control spec_item_show" name="spec_item_show_Ebw31844lN4qo0iIq0iwIob421nI1Xo4[]" value="1">
//     <input type="hidden" class="form-control spec_item_id" name="spec_item_id_Ebw31844lN4qo0iIq0iwIob421nI1Xo4[]" value="y8AEEnJ8800LpD65wj51Z1zN6EG81l88">
//     <div class="input-group" style="margin:10px 0;">
//         <span class="input-group-addon">
//             <label class="checkbox-inline" style="margin-top:-20px;">
//                 <input type="checkbox" checked="" value="1" onclick="showItem(this)">
//             </label>
//         </span>
//         <input type="text" class="form-control spec_item_title error" name="spec_item_title_Ebw31844lN4qo0iIq0iwIob421nI1Xo4[]" value="">
//         <span class="input-group-addon">
//             <a href="javascript:;" onclick="removeSpecItem(this)" title="删除"><i class="fa fa-times"></i></a>
//             <a href="javascript:;" class="fa fa-arrows" title="拖动调整显示顺序" style="display:none;"></a>
//         </span>
//     </div>
    
//     <div>

//      </div>
// </div>


// </div>
//         </div>
//     </div>   
//           <div class="form-group">
//             <label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
//             <div class="col-sm-9 col-xs-12">
//                 <a href="javascript:;" id="add-specitem-Ebw31844lN4qo0iIq0iwIob421nI1Xo4" specid="Ebw31844lN4qo0iIq0iwIob421nI1Xo4" class="btn btn-info add-specitem" onclick="addSpecItem('Ebw31844lN4qo0iIq0iwIob421nI1Xo4')"><i class="fa fa-plus"></i> 添加规格项</a>
//                 <a href="javascript:void(0);" class="btn btn-danger" onclick="removeSpec('Ebw31844lN4qo0iIq0iwIob421nI1Xo4')"><i class="fa fa-plus"></i> 删除规格</a>
//             </div>
         
//     </div>
//    </div> 
// </div>
// TEST;
			  // $form->html('<div id="specs"></div>');
				// $form->hasMany('skus', function ($form) {

				// 	// $form->->row(3);
				// 	// $form->->row(3);

				// 	// $form->number('stock', '剩余库存')->min(0)->rules('required|integer|min:0');
				// 	// $form->number('weight', '重量')->min(0)->rules('required|integer|min:0')->help('单位g');
				// });

			})->tab('核销', function ($form) {
				$form->radio('is_verify', '适合核销')->options(['0' => '否', '1' => '是'])->default(0);
			});
			// 定义事件回调，当模型即将保存时会触发这个回调
			// $form->saving(function (Form $form) {
			// 	// $form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;


			// });
            $form->saved(function(Form $form){
                $id = $form->model()->id;
                        $spec_ids    = request()->input('spec_id');
        $spec_titles = request()->input('spec_title');
        $specids     = array();
        $len         = count($spec_ids);
        $specids     = array();
        $spec_items  = array();
        for ($k = 0; $k < $len; $k++) {
            $spec_id     = "";
            $get_spec_id = $spec_ids[$k];
            $a           = array(
                // "uniacid" => $_W['uniacid'],
                "product_id" => $id,
                // "displayorder" => $k,
                "title" => $spec_titles[$get_spec_id]
            );
            if (is_numeric($get_spec_id)) {
                \DB::table('product_spec')->where('id',$get_spec_id)->update($a);
                // pdo_update("eshop_goods_spec", $a, array(
                //     "id" => $get_spec_id
                // ));
                $spec_id = $get_spec_id;
            } else {
                // pdo_insert('eshop_goods_spec', $a);
                $spec_id = \DB::table('product_spec')->insertGetId($a);
            }
            $spec_item_ids       = request()->input("spec_item_id_" . $get_spec_id);
            $spec_item_titles    = request()->input("spec_item_title_" . $get_spec_id);
            // $spec_item_shows     = request()->input("spec_item_show_" . $get_spec_id);
            // $spec_item_thumbs    = request()->input("spec_item_thumb_" . $get_spec_id);
            // $spec_item_oldthumbs = request()->input("spec_item_oldthumb_" . $get_spec_id);
            // $spec_item_virtuals  = 0;
            $itemlen             = count($spec_item_ids);
            $itemids             = array();
            for ($n = 0; $n < $itemlen; $n++) {
                $item_id     = "";
                $get_item_id = $spec_item_ids[$n];
                $d   = array(
                    "spec_id" => $spec_id,
                    // "displayorder" => $n,
                    "title" => $spec_item_titles[$n],
                );
                // $f = "spec_item_thumb_" . $get_item_id;
                if (is_numeric($get_item_id)) {
                    \DB::table('product_spec_item')->where('id',$get_item_id)->update($d);
                    // pdo_update("eshop_goods_spec_item", $d, array(
                    //     "id" => $get_item_id
                    // ));
                    $item_id = $get_item_id;
                } else {
                    // pdo_insert('eshop_goods_spec_item', $d);
                    $item_id =  \DB::table('product_spec_item')->insertGetId($d);
                }
                $itemids[]    = $item_id;
                $d['get_id']  = $get_item_id;
                $d['id']      = $item_id;
                $spec_items[] = $d;
            }
            if (count($itemids) > 0) {
                \DB::table('product_spec_item')->where('spec_id',$spec_id)->whereNotIn('id',$itemids)->delete();
                // pdo_query("delete from " . tablename('eshop_goods_spec_item') . " where uniacid={$_W['uniacid']} and specid=$spec_id and id not in (" . implode(",", $itemids) . ")");
            } else {
                \DB::table('product_spec_item')->where('spec_id',$spec_id)->delete();
                // pdo_query('delete from ' . tablename('eshop_goods_spec_item') . " where uniacid={$_W['uniacid']} and specid=$spec_id");
            }
            \DB::table('product_spec')->where('id',$spec_id)->update(['content' => serialize($itemids)]);
            // pdo_update('eshop_goods_spec', array(
            //     'content' => serialize($itemids)
            // ), array(
            //     "id" => $spec_id
            // ));
            $specids[] = $spec_id;
        }
        if (count($specids) > 0) {
            \DB::table('product_spec')->where('product_id',$id)->whereNotIn('id',$specids)->delete();
            // pdo_query("delete from " . tablename('eshop_goods_spec') . " where uniacid={$_W['uniacid']} and goodsid=$id and id not in (" . implode(",", $specids) . ")");
        } else {
            \DB::table('product_spec')->where('product_id',$id)->delete();
            // pdo_query('delete from ' . tablename('eshop_goods_spec') . " where uniacid={$_W['uniacid']} and goodsid=$id");
                }

        $option_idss          = request()->input('option_ids');
        $option_productprices = request()->input('option_productprice');
        $option_marketprices  = request()->input('option_marketprice');
        $option_costprices    = request()->input('option_costprice');
        $option_stocks        = request()->input('option_stock');
        $option_weights       = request()->input('option_weight');
        $option_goodssns      = request()->input('option_goodssn');
        $option_productssns   = request()->input('option_productsn');
        $len                  = count($option_idss);
        $optionids            = array();
        for ($k = 0; $k < $len; $k++) {
            $option_id     = "";
            $ids           = $option_idss[$k];
            $get_option_id = request()->input('option_id_' . $ids)[0];
            $idsarr        = explode("_", $ids);
            $newids        = array();
            foreach ($idsarr as $key => $ida) {
                foreach ($spec_items as $it) {
                    if ($it['get_id'] == $ida) {
                        $newids[] = $it['id'];
                        break;
                    }
                }
            }
            $newids = implode("_", $newids);
            $a      = array(
                "title" => request()->input('option_title_' . $ids)[0],
                "price" => request()->input('option_productprice_' . $ids)[0],
                "cost" => request()->input('option_costprice_' . $ids)[0],
                "price_on_app" => request()->input('option_marketprice_' . $ids)[0],
                "stock" => request()->input('option_stock_' . $ids)[0],
                "weight" => request()->input('option_weight_' . $ids)[0],
                // "goodssn" => request()->input('option_goodssn_' . $ids)[0],
                "product_sn" => request()->input('option_productsn_' . $ids)[0],
                "product_id" => $id,
                "specs" => $newids,
                // 'virtual' => $data['type'] == 3 ? $_GPC['option_virtual_' . $ids][0] : 0
            );
            //$totalstocks += $a['stock'];
            if (empty($get_option_id)) {
                // pdo_insert("eshop_goods_option", $a);
                $option_id = \DB::table('product_skus')->insertGetId($a);
            } else {
                // pdo_update('eshop_goods_option', $a, array(
                //     'id' => $get_option_id
                // ));
                \DB::table('product_skus')->where('id',$get_option_id)->update($a);
                $option_id = $get_option_id;
            }
            $optionids[] = $option_id;
        }
        if (count($optionids) > 0) {
            \DB::table('product_skus')->where('product_id',$id)->whereNotIn('id',$optionids)->delete();
            // pdo_query("delete from " . tablename('eshop_goods_option') . " where goodsid=$id and id not in ( " . implode(',', $optionids) . ")");
        } else {
             \DB::table('product_skus')->where('product_id',$id)->delete();
            // pdo_query('delete from ' . tablename('eshop_goods_option') . " where goodsid=$id");
        }
        // if ($data['type'] == 3 ) {
        //     $pv->updateGoodsStock($id);
        // } else {
        //     if (($totalstocks > 0) && ($data['totalcnf'] != 2)) {
        //         pdo_update("eshop_goods", array(
        //             "total" => $totalstocks
        //         ), array(
        //             "id" => $id
        //         ));
        //     }
        // }




            });
		});
	}

}
