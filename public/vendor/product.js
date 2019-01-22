 $(function(){

 if($('input[name="has_sku"]:checked').val() == 1){
     $('#specTable').show();
 }
 $('input[name="has_sku"]').on('ifChecked', function() {
     $('#specTable').toggle(null, null, !$(this).val());
 });
 $("input.file_upload").on('filesorted', function(e, params) {
     console.log('File sorted params', params);
 });
 $('#add-spec').click(function() {
     var len = $(".spec_item").length;

     // if(type==3 && virtual==0 && len>=1){
     //     util.message('您的商品类型为：虚拟物品(卡密)的多规格形式，只能添加一种规格！');
     //     return;
     // }

     $("#add-spec").html("正在处理...").attr("disabled", "true").toggleClass("btn-primary");
     var url = "/api/v1/tpl?tpl=spec";
     $.ajax({
         "url": url,
         success: function(data) {
             $("#add-spec").html('<i class="fa fa-plus"></i> 添加规格').removeAttr("disabled").toggleClass("btn-primary");;
             $('#specs').append(data);
             var len = $(".add-specitem").length - 1;
             $(".add-specitem:eq(" + len + ")").focus();

             window.optionchanged = true;
         }
     });
 });
 window.removeSpec = function(specid) {
     if (confirm('确认要删除此规格?')) {
         $("#spec_" + specid).remove();
         window.optionchanged = true;
     }
 }
 window.addSpecItem = function(specid) {
     $("#add-specitem-" + specid).html("正在处理...").attr("disabled", "true");
     var url = "/api/v1/tpl?tpl=specitem" + "&specid=" + specid;
     $.ajax({
         "url": url,
         success: function(data) {
             $("#add-specitem-" + specid).html('<i class="fa fa-plus"></i> 添加规格项').removeAttr("disabled");
             $('#spec_item_' + specid).append(data);
             var len = $("#spec_" + specid + " .spec_item_title").length - 1;
             $("#spec_" + specid + " .spec_item_title:eq(" + len + ")").focus();
             window.optionchanged = true;
            //  if (type == 3 && virtual == 0) {
            //      $(".choosetemp").show();
            //  }
         }
     });
 }
 window.removeSpecItem = function(obj) {
     $(obj).parent().parent().parent().remove();
 }
 window.calc = function() {

     window.optionchanged = false;
     var html = '<table class="table table-bordered table-condensed"><thead><tr class="active">';
     var specs = [];
     if ($('.spec_item').length <= 0) {
         $("#options").html('');
         return;
     }
     $(".spec_item").each(function(i) {
         var _this = $(this);

         var spec = {
             id: _this.find(".spec_id").val(),
             title: _this.find(".spec_title").val()
         };

         var items = [];
         _this.find(".spec_item_item").each(function() {
             var __this = $(this);
             var item = {
                 id: __this.find(".spec_item_id").val(),
                 title: __this.find(".spec_item_title").val(),
                 virtual: __this.find(".spec_item_virtual").val(),
                 show: __this.find(".spec_item_show").get(0).checked ? "1" : "0"
             }
             items.push(item);
         });
         spec.items = items;
         specs.push(spec);
     });
     specs.sort(function(x, y) {
         if (x.items.length > y.items.length) {
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
     for (var i = 0; i < len; i++) {
         html += "<th style='width:80px;'>" + specs[i].title + "</th>";
         var itemlen = specs[i].items.length;
         if (itemlen <= 0) { itemlen = 1 };
         newlen *= itemlen;
         h[i] = new Array(newlen);
         for (var j = 0; j < newlen; j++) {
             h[i][j] = new Array();
         }
         var l = specs[i].items.length;
         rowspans[i] = 1;
         for (j = i + 1; j < len; j++) {
             rowspans[i] *= specs[j].items.length;
         }
     }

     html += '<th class="info" style="width:130px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">库存</div><div class="input-group"><input type="text" class="form-control option_stock_all" VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_stock\');"></a></span></div></div></th>';
     html += '<th class="success" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">销售价格</div><div class="input-group"><input type="text" class="form-control option_marketprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_marketprice\');"></a></span></div></div></th>';
     html += '<th class="warning" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">市场价格</div><div class="input-group"><input type="text" class="form-control option_productprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productprice\');"></a></span></div></div></th>';
     html += '<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">成本价格</div><div class="input-group"><input type="text" class="form-control option_costprice_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_costprice\');"></a></span></div></div></th>';
     html += '<th class="primary" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品编码</div><div class="input-group"><input type="text" class="form-control option_goodssn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_goodssn\');"></a></span></div></div></th>';
     html += '<th class="danger" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">商品条码</div><div class="input-group"><input type="text" class="form-control option_productsn_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_productsn\');"></a></span></div></div></th>';
     html += '<th class="info" style="width:150px;"><div class=""><div style="padding-bottom:10px;text-align:center;font-size:16px;">重量（克）</div><div class="input-group"><input type="text" class="form-control option_weight_all"VALUE=""/><span class="input-group-addon"><a href="javascript:;" class="fa fa-hand-o-down" title="批量设置" onclick="setCol(\'option_weight\');"></a></span></div></div></th>';
     html += '</tr></thead>';

     for (var m = 0; m < len; m++) {
         var k = 0,
             kid = 0,
             n = 0;
         for (var j = 0; j < newlen; j++) {
             var rowspan = rowspans[m];
             if (j % rowspan == 0) {
                 h[m][j] = {
                     title: specs[m].items[kid].title,
                     virtual: specs[m].items[kid].virtual,
                     html: "<td rowspan='" + rowspan + "'>" + specs[m].items[kid].title + "</td>",
                     id: specs[m].items[kid].id
                 };
             } else {
                 h[m][j] = { title: specs[m].items[kid].title, virtual: specs[m].items[kid].virtual, html: "", id: specs[m].items[kid].id };
             }
             n++;
             if (n == rowspan) {
                 kid++;
                 if (kid > specs[m].items.length - 1) { kid = 0; }
                 n = 0;
             }
         }
     }

     var hh = "";
     for (var i = 0; i < newlen; i++) {
         hh += "<tr>";
         var ids = [];
         var titles = [];
         var virtuals = [];
         for (var j = 0; j < len; j++) {
             hh += h[j][i].html;
             ids.push(h[j][i].id);
             titles.push(h[j][i].title);
             virtuals.push(h[j][i].virtual);
         }
         ids = ids.join('_');
         titles = titles.join('+');

         var val = { id: "", title: titles, stock: "", costprice: "", productprice: "", marketprice: "", weight: "", productsn: "", goodssn: "", virtual: virtuals };
         if ($(".option_id_" + ids).length > 0) {
             val = {
                 id: $(".option_id_" + ids + ":eq(0)").val(),
                 title: titles,
                 stock: $(".option_stock_" + ids + ":eq(0)").val(),
                 costprice: $(".option_costprice_" + ids + ":eq(0)").val(),
                 productprice: $(".option_productprice_" + ids + ":eq(0)").val(),
                 marketprice: $(".option_marketprice_" + ids + ":eq(0)").val(),
                 goodssn: $(".option_goodssn_" + ids + ":eq(0)").val(),
                 productsn: $(".option_productsn_" + ids + ":eq(0)").val(),
                 weight: $(".option_weight_" + ids + ":eq(0)").val(),
                 virtual: virtuals
             }
         }
         hh += '<td class="info">'
         hh += '<input name="option_stock_' + ids + '[]"type="text" class="form-control option_stock option_stock_' + ids + '" value="' + (val.stock == 'undefined' ? '' : val.stock) + '"/></td>';
         hh += '<input name="option_id_' + ids + '[]"type="hidden" class="form-control option_id option_id_' + ids + '" value="' + (val.id == 'undefined' ? '' : val.id) + '"/>';
         hh += '<input name="option_ids[]"type="hidden" class="form-control option_ids option_ids_' + ids + '" value="' + ids + '"/>';
         hh += '<input name="option_title_' + ids + '[]"type="hidden" class="form-control option_title option_title_' + ids + '" value="' + (val.title == 'undefined' ? '' : val.title) + '"/></td>';
         hh += '<input name="option_virtual_' + ids + '[]"type="hidden" class="form-control option_title option_title_' + ids + '" value="' + (val.virtual == 'undefined' ? '' : val.virtual) + '"/></td>';
         hh += '</td>';
         hh += '<td class="success"><input name="option_marketprice_' + ids + '[]" type="text" class="form-control option_marketprice option_marketprice_' + ids + '" value="' + (val.marketprice == 'undefined' ? '' : val.marketprice) + '"/></td>';
         hh += '<td class="warning"><input name="option_productprice_' + ids + '[]" type="text" class="form-control option_productprice option_productprice_' + ids + '" " value="' + (val.productprice == 'undefined' ? '' : val.productprice) + '"/></td>';
         hh += '<td class="danger"><input name="option_costprice_' + ids + '[]" type="text" class="form-control option_costprice option_costprice_' + ids + '" " value="' + (val.costprice == 'undefined' ? '' : val.costprice) + '"/></td>';
         hh += '<td class="primary"><input name="option_goodssn_' + ids + '[]" type="text" class="form-control option_goodssn option_goodssn_' + ids + '" " value="' + (val.goodssn == 'undefined' ? '' : val.goodssn) + '"/></td>';
         hh += '<td class="danger"><input name="option_productsn_' + ids + '[]" type="text" class="form-control option_productsn option_productsn_' + ids + '" " value="' + (val.productsn == 'undefined' ? '' : val.productsn) + '"/></td>';
         hh += '<td class="info"><input name="option_weight_' + ids + '[]" type="text" class="form-control option_weight option_weight_' + ids + '" " value="' + (val.weight == 'undefined' ? '' : val.weight) + '"/></td>';
         hh += "</tr>";
     }
     html += hh;
     html += "</table>";
     $("#options").html(html);
 }
 window.setCol = function(cls) {
     $("." + cls).val($("." + cls + "_all").val());
 }
});