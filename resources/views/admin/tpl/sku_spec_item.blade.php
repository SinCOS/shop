<div class="spec_item_item" style="float:left;margin:0 5px 10px 0;width:250px;">
	<input type="hidden" class="form-control spec_item_show" name="spec_item_show_{{$spec['id']}}[]" VALUE="{{$specitem['show']}}" />
	<input type="hidden" class="form-control spec_item_id" name="spec_item_id_{{$spec['id']}}[]" VALUE="{{$specitem['id']}}" />
	<div class="input-group"  style="margin:10px 0;">
		<span class="input-group-addon">
			<label class="checkbox-inline" style="margin-top:-20px;">

					{{-- expr --}}
				
				<input type="checkbox" @if ($specitem['show']==1) checked @endif value="1" onclick='showItem(this)'>
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


