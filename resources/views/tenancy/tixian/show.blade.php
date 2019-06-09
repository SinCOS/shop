<div class="box box-info">
    <div class="box-header with-border">
      <h3 class="box-title">订单流水号：{{ $detail->id }}</h3>
      <div class="box-tools">
        <div class="btn-group pull-right" style="margin-right: 10px">
          <a href="/withdraw" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
        </div>
      </div>
    </div>
    <div class="box-body">
      <table class="table table-bordered">
        <tbody>
            <tr>
                <td>提现金额</td>
                <td>{{$detail->number}}</td>
            </tr>
            <tr>
                    <td>申请时间：</td>
  
                    <td>
                    
                          {{ $detail->created_at->format('Y-m-d H:i:s') }}
                  
                      
            
                    </td>
            </tr>
            <tr>
                <td>账户</td>
                <td>{{$detail->user->mobile}}</td>
            </tr>
        <tr>
          <td>姓名</td>
          <td>{{ $detail->bankinfo['name'] }}</td>
         
        </tr>
        <tr>
          <td>银行</td>
          <td>{{ $detail->bankinfo['bankname'] }}</td>
          
        </tr>
        <tr>
            <td>
                银行地址
            </td>
            <td>
                {{$detail->bankinof['zhihang_name']}}
            </td>
        </tr>
        <tr>
                <td>银行卡号：</td>
                <td>{{  $detail->bankinfo['banknumber'] }}</td>
        </tr>

   



       
        <tr>
         
          <td>
            @if($detail->status == 0)
            <button class="btn btn-sm btn-success" id="btn-refund-agree">同意</button>
            <button class="btn btn-sm btn-danger" id="btn-refund-disagree">不同意</button>
            @else
                @if ($detail->status ==1 )
                    
                    <span class='alert-sccess'>已提现</span>
                @else
                    <span class='alert-error'>提现驳回</span>
                @endif
            @endif
          </td>
        </tr>
       
        </tbody>
      </table>
    </div>
  </div>
  
  <script>
  $(document).ready(function() {
    // 不同意 按钮的点击事件
    $('#btn-refund-disagree').click(function() {
    // 注意：Laravel-Admin 的 swal 是 v1 版本，参数和 v2 版本的不太一样
      swal({
        title: '确认拒绝此请求',
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "确认",
        cancelButtonText: "取消",
      }).then(function(isConfirm){
        // 用户点击了取消，inputValue 为 false
        // === 是为了区分用户点击取消还是没有输入
        console.log(isConfirm);
        if (isConfirm.dismiss == 'cancel') {
          return;
        }
       
       
        // Laravel-Admin 没有 axios，使用 jQuery 的 ajax 方法来请求
        $.ajax({
          url: '{{ route('tenancy.withdraw.do', [$detail->id]) }}',
          type: 'POST',
          data: JSON.stringify({   // 将请求变成 JSON 字符串
            agree: false,  // 拒绝申请
            //reason: inputValue,
            // 带上 CSRF Token
            // Laravel-Admin 页面里可以通过 LA.token 获得 CSRF Token
            _token: LA.token,
          }),
          contentType: 'application/json',  // 请求的数据格式为 JSON
          success: function (data) {  // 返回成功时会调用这个函数
            swal({
              title: '操作成功',
              type: 'success'
            }, function() {
              // 用户点击 swal 上的 按钮时刷新页面
              location.reload();
            });
          },error: function(data){
            swal({
              title: data.responseJSON.msg,
              type: 'error'
            }, function() {
              // 用户点击 swal 上的 按钮时刷新页面
              location.reload();
            });
              console.log(data.responseJSON.msg);
          }
        });
      });
    });
  
    // 同意按钮的点击事件
    $('#btn-refund-agree').click(function() {
      swal({
        title: '确认要打款给用户？',
        type: 'warning',
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "确认",
        cancelButtonText: "取消",
      }).then(function(ret){
       
        // 用户点击取消，不做任何操作
        if (!ret) {
          return;
        }
        
        $.ajax({
          url: '{{ route('tenancy.withdraw.do', [$detail->id]) }}',
          type: 'POST',
          data: JSON.stringify({
            agree: true, // 代表同意退款
            _token: LA.token,
          }),
          contentType: 'application/json',
          success: function (data) {
            swal({
              title: '操作成功',
              type: 'success'
            }, function() {
              location.reload();
            });
          },error: function(data){
            swal({
              title: data.responseJSON.msg,
              type: 'error'
            }, function() {
              // 用户点击 swal 上的 按钮时刷新页面
              location.reload();
            });
              console.log(data.responseJSON.msg);
          }
        });
      });
    });
  
  });
  </script>