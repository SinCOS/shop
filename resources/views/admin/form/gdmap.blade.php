
<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id['id']}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
   

    <div class="{{$viewClass['field']}}">
    <input type="hidden" name="{{$id['id']}}" value="{{old($column['id'],$value['id'])}}" {!! $attributes !!}>
        @include('admin::form.error')
    <div id="{{$id['id']}}" style="width: 100%;height:500px;"></div>
    </div>
        <div class="input-card" style="width: 200px">
   <h4 style="margin-bottom: 10px; font-weight: 600">右键完成绘制</h4>
   {{-- <a class="btn" onclick="drawPolyline()" style="margin-bottom: 5px">绘制线段</a>  --}}
   <a class="btn poly"  style="margin-bottom: 5px">绘制多边形</a> 
   {{-- <a class="btn" onclick="drawRectangle()" style="margin-bottom: 5px">绘制矩形</a>  --}}
   <a class="btn clearMap" " style="margin-bottom: 5px">重设</a> 
</div>
    </div>
