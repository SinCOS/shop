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
			$grid->id('ID')->sortable();
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

	/**
	 * Make a form builder.
	 *
	 * @return Form
	 */
	protected function form() {

		// 创建一个表单
		return Admin::form(Product::class, function (Form $form) {
			$form->tab('商品信息', function ($form) {
				// 创建一个输入框，第一个参数 title 是模型的字段名，第二个参数是该字段描述
				$form->select('category_id', '分类')->options(Category::selectOptions(null, '请选择'));
				$form->text('title', '商品名称')->rules('required');
				// 创建一个选择图片的框
				$form->image('image', '封面图片')->rules('required|image')->uniqueName()->move('products/covers/' . date('Y-m-d'));
				$form->hidden('shop_id')->default('1');
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
                    Admin::js('/vendor/laravel-admin/bootstrap-fileinput/js/plugins/sortable.min.js');
				// $form->hasMany('images','主图',function(Form\NestedForm $form){
				//     $form->image('img','图片')->uniqueName()->move('products/lists/' .date('Y-m-d'));
				// });
				// 创建一个富文本编辑器
				// $form->editor('description', '商品描述')->rules('required');
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
				// $form->row(function ($row) use ($form) {
				// 	$row->width(3)->text('title', 'SKU 名称')->rules('required');
				// 	$row->width(3)->text('description', 'SKU 描述')->rules('required');
				// 	$row->width(3)->currency('price', '单价')->symbol('￥')->rules('required|numeric|min:0.01');
				// 	$row->width(3)->currency('price_on_app', '平台价')->symbol('￥')->rules('required|numeric|min:0.01');
				// });
$table = <<<TABLE
<div id="specs">
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
    </div>
TABLE;
    $form->html($table);
$tableScript = <<<TST
   $('input[name="has_sku"]').on('ifChecked',function(){
        $('#specTable').toggle(null,null,! $(this).val());
    });
    $("input.file_upload").on('filesorted',function(e,params){
    console.log('File sorted params', params);
    });
    $('#add-spec').click(function(){
                    var len = $(".spec_item").length;
               
                    // if(type==3 && virtual==0 && len>=1){
                    //     util.message('您的商品类型为：虚拟物品(卡密)的多规格形式，只能添加一种规格！');
                    //     return;
                    // }
                    
    $("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
        var url = "/api/v1/tpl?tpl=spec";
        $.ajax({
            "url": url,
            success:function(data){
                $("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary"); ;
                $('#specs').append(data);
                var len = $(".add-specitem").length -1;
                $(".add-specitem:eq(" +len+ ")").focus();
                                                                                
                window.optionchanged = true;
            }
        });
    });
    window.removeSpec = function(specid){
        if (confirm('确认要删除此规格?')){
            $("#spec_" + specid).remove();
            window.optionchanged = true;
        }
    }
    window.addSpecItem = function(specid){
    $("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
        var url = "/api/v1/tpl?tpl=specitem" + "&specid=" + specid;
        $.ajax({
            "url": url,
            success:function(data){
                $("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
                $('#spec_item_' + specid).append(data);
                var len = $("#spec_" + specid + " .spec_item_title").length -1;
                $("#spec_" + specid + " .spec_item_title:eq(" +len+ ")").focus();
                window.optionchanged = true;
                                                                        if(type==3 && virtual==0){
                                                                                    $(".choosetemp").show();
                                                                         }
            }
        });
    }
    window.removeSpecItem = function(obj){
        $(obj).parent().parent().parent().remove();
    }
    window.calc = function(){
    window.optionchanged = false;
    var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
    var specs = [];
         if($('.spec_item').length<=0){
             $("#options").html('');
             return;
         }
    $(".spec_item").each(function(i){
        var _this = $(this);

        var spec = {
            id: _this.find(".spec_id").val(),
            title: _this.find(".spec_title").val()
        };
    
        var items = [];
        _this.find(".spec_item_item").each(function(){
            var __this = $(this);
            var item = {
                id: __this.find(".spec_item_id").val(),
                title: __this.find(".spec_item_title").val(),
                                                                        virtual: __this.find(".spec_item_virtual").val(),
                show:__this.find(".spec_item_show").get(0).checked?"1":"0"
            }
            items.push(item);
        });
        spec.items = items;
        specs.push(spec);
    });
    specs.sort(function(x,y){
        if (x.items.length > y.items.length){
            return 1;
        }
        if (x.items.length < y.items.length) {
            return -1;
        }
    });

    var len = specs.length;
    var newlen = 1; 
    var h = new Array(len); 
    var rowspans = new Array(len); 
    for(var i=0;i<len;i++){
        html+="<th style='width:80px;'>" + specs[i].title + "</th>";
        var itemlen = specs[i].items.length;
        if(itemlen<=0) { itemlen = 1 };
        newlen*=itemlen;
        h[i] = new Array(newlen);
        for(var j=0;j<newlen;j++){
            h[i][j] = new Array();
        }
        var l = specs[i].items.length;
        rowspans[i] = 1;
        for(j=i+1;j<len;j++){
            rowspans[i]*= specs[j].items.length;
        }
    }

    html += '<th class="info" style="width:130px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
    html += '<th class="success" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
    html+='<th class="warning" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
    html+='<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
                   html+='<th class="primary" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
                   html+='<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_productsn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
    html+='<th class="info" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
    html+='</tr></thead>';
    
    for(var m=0;m<len;m++){
        var k = 0,kid = 0,n=0;
        for(var j=0;j<newlen;j++){
            var rowspan = rowspans[m]; 
            if( j % rowspan==0){
                h[m][j]={title: specs[m].items[kid].title, virtual: specs[m].items[kid].virtual,html: "<td rowspan='" +rowspan + "'>"+ specs[m].items[kid].title+"</td>",id: specs[m].items[kid].id};
            }
            else{
                h[m][j]={title:specs[m].items[kid].title,virtual: specs[m].items[kid].virtual, html: "",id: specs[m].items[kid].id};    
            }
            n++;
            if(n==rowspan){
            kid++; if(kid>specs[m].items.length-1) { kid=0; }
            n=0;
            }
        }
    }
 
    var hh = "";
    for(var i=0;i<newlen;i++){
        hh+="<tr>";
        var ids = [];
        var titles = [];    
                                    var virtuals = [];
        for(var j=0;j<len;j++){
            hh+=h[j][i].html; 
            ids.push( h[j][i].id);
            titles.push( h[j][i].title);
                                                      virtuals.push( h[j][i].virtual);
        }
        ids =ids.join('_');
        titles= titles.join('+');
    
        var val ={ id : "",title:titles, stock : "",costprice : "",productprice : "",marketprice : "",weight:"",productsn:"",goodssn:"",virtual:virtuals };
        if( $(".option_id_" + ids).length>0){
            val ={
                id : $(".option_id_" + ids+":eq(0)").val(),
                title: titles,
                stock : $(".option_stock_" + ids+":eq(0)").val(),
                costprice : $(".option_costprice_" + ids+":eq(0)").val(),
                productprice : $(".option_productprice_" + ids+":eq(0)").val(),
                marketprice : $(".option_marketprice_" + ids +":eq(0)").val(),
                                                                        goodssn : $(".option_goodssn_" + ids +":eq(0)").val(),
                                                                        productsn : $(".option_productsn_" + ids +":eq(0)").val(),
                weight : $(".option_weight_" + ids+":eq(0)").val(),
                                  virtual : virtuals
            }
        }
        hh += '<td class="info">'
        hh += '<input name="option_stock_' + ids +'[]"type="text" class="form-control option_stock option_stock_' + ids +'" value="' +(val.stock=='undefined'?'':val.stock )+'"/></td>';
        hh += '<input name="option_id_' + ids+'[]"type="hidden" class="form-control option_id option_id_' + ids +'" value="' +(val.id=='undefined'?'':val.id )+'"/>';
        hh += '<input name="option_ids[]"type="hidden" class="form-control option_ids option_ids_' + ids +'" value="' + ids +'"/>';
        hh += '<input name="option_title_' + ids +'[]"type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.title=='undefined'?'':val.title )+'"/></td>';
                                    hh += '<input name="option_virtual_' + ids +'[]"type="hidden" class="form-control option_title option_title_' + ids +'" value="' +(val.virtual=='undefined'?'':val.virtual )+'"/></td>';
        hh += '</td>';
        hh += '<td class="success"><input name="option_marketprice_' + ids+'[]" type="text" class="form-control option_marketprice option_marketprice_' + ids +'" value="' +(val.marketprice=='undefined'?'':val.marketprice )+'"/></td>';
        hh += '<td class="warning"><input name="option_productprice_' + ids+'[]" type="text" class="form-control option_productprice option_productprice_' + ids +'" " value="' +(val.productprice=='undefined'?'':val.productprice )+'"/></td>';
        hh += '<td class="danger"><input name="option_costprice_' +ids+'[]" type="text" class="form-control option_costprice option_costprice_' + ids +'" " value="' +(val.costprice=='undefined'?'':val.costprice )+'"/></td>';
                                    hh += '<td class="primary"><input name="option_goodssn_' +ids+'[]" type="text" class="form-control option_goodssn option_goodssn_' + ids +'" " value="' +(val.goodssn=='undefined'?'':val.goodssn )+'"/></td>';
                                    hh += '<td class="danger"><input name="option_productsn_' +ids+'[]" type="text" class="form-control option_productsn option_productsn_' + ids +'" " value="' +(val.productsn=='undefined'?'':val.productsn )+'"/></td>';
        hh += '<td class="info"><input name="option_weight_' + ids +'[]" type="text" class="form-control option_weight option_weight_' + ids +'" " value="' +(val.weight=='undefined'?'':val.weight )+'"/></td>';
        hh += "</tr>";
    }
    html+=hh;
    html+="</table>";
    $("#options").html(html);
}
window.setCol= function(cls){
    $("."+cls).val( $("."+cls+"_all").val());
}
TST;
    Admin::script($tableScript);
             
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
			$form->saving(function (Form $form) {
				$form->model()->price = collect($form->input('skus'))->where(Form::REMOVE_FLAG_NAME, 0)->min('price') ?: 0;
			});
		});
	}

}
