@foreach ($allspecs as $spec)
<style type='text/css'>
    .multi-item { height:110px;float:left;position:relative;}
     .img-thumbnail { width:100px;height:100px}
     .img-nickname { position: absolute;bottom:0px;line-height:25px;height:25px;
                    color:#fff;text-align:center;width:100px;top-25px;background:rgba(0,0,0,0.8);}
     .multi-img-details { padding:5px;}
</style>
     <div class='panel panel-default spec_item' id='spec_{{$spec['id']}}' >
         <div class='panel-body'>
	<input name="spec_id[]" type="hidden" class="form-control spec_id" value="{{$spec['id']}}"/>
	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">规格名</label>
	<div class="col-sm-9 col-xs-12">
         
			<input name="spec_title[{{$spec['id']}}]" type="text" class="form-control  spec_title" value="{{$spec['title']}}" placeholder="(比如: 颜色)"/>
                      
		</div>
	</div>



	<div class="form-group">
		<label class="col-xs-12 col-sm-3 col-md-2 control-label">规格项</label>
	<div class="col-sm-9 col-xs-12">
			<div id='spec_item_{{$spec['id']}}' class='spec_item_items'>
					@if (is_array($spec['items']))
						@foreach ($spec['items'] as $specitem)
							<div class="spec_item_item" style="float:left;margin:0 5px 10px 0;width:250px;">
								<input type="hidden" class="form-control spec_item_show" name="spec_item_show_{{$spec['id']}}[]" VALUE="1" />
								<input type="hidden" class="form-control spec_item_id" name="spec_item_id_{{$spec['id']}}[]" VALUE="{{$specitem['id']}}" />
								<div class="input-group"  style="margin:10px 0;">
									<span class="input-group-addon">
										<label class="checkbox-inline" style="margin-top:-20px;">

												{{-- expr --}}
											
											<input type="checkbox"  checked value="1" onclick='showItem(this)'>
										</label>
									</span>

									<input type="text" class="form-control spec_item_title error" name="spec_item_title_{{$spec['id']}}[]" VALUE="{{$specitem['title']}}" />
									<span class="input-group-addon">
										<a href="javascript:;" onclick="removeSpecItem(this)" title='删除'><i class="fa fa-times"></i></a>
								  		<!-- <a href="javascript:;" class="fa fa-arrows" title="拖动调整显示顺序" ></a> -->
									</span>
								</div>
							  

							  
								<div>
									
								</div>
							</div>



						@endforeach
					@endif
					
			</div>
		</div>
	</div>   
          <div class="form-group">
			<label class="col-xs-12 col-sm-3 col-md-2 control-label"></label>
			<div class="col-sm-9 col-xs-12">
				<a href="javascript:;" id="add-specitem-{{$spec['id']}}" specid='{{$spec['id']}}' class='btn btn-info add-specitem' onclick="addSpecItem('{{$spec['id']}}')"><i class="fa fa-plus"></i> 添加规格项</a>
				<a href="javascript:void(0);" class='btn btn-danger' onclick="removeSpec('{{$spec['id']}}')"><i class="fa fa-plus"></i> 删除规格</a>
			</div>
		 
	</div>
   </div> 
</div>

@endforeach