<?php

namespace app\Admin\Field;

use Encore\Admin\Form\Field;

class GDMap extends Field
{
    /**
     * Column name.
     *
     * @var array
     */
    protected $column = [];
    protected $view = 'admin.form.gdmap';
    /**
     * Get assets required by this field.
     *
     * @return array
     */
    public static function getAssets()
    {
     $js = ["https://webapi.amap.com/maps?v=1.4.11&key=fcd42350612b738fc7b65159844f51a5&plugin=AMap.MouseTool",
            "https://a.amap.com/jsapi_demos/static/demo-center/js/demoutils.js"];
     
        return compact('js');
    }

    public function __construct($column, $arguments)
    {
        $this->column['id'] = (string)$column;
        // $this->column['lng'] = (string)$arguments[0];

        //array_shift($arguments);

        $this->label = $this->formatLabel($arguments);
        $this->id = $this->formatId($this->column);
        // var_dump($this->id);
        /*
         * Google map is blocked in mainland China
         * people in China can use Tencent map instead(;
         */
        $this->useGDMap();
    }





    public function useGDMap()
    {
        $this->script = <<<EOT
$(function(){
    var map = new AMap.Map("{$this->id['id']}", {
        // center:,
        zoom: 14
    });
    var start = false;
    var marks = [];
    AMap.event.addListener(map,'click',function(e){
        if(start) marks.push(e.lnglat.lng + " " + e.lnglat.lat);
        console.log(e);
    });
    var mouseTool = new AMap.MouseTool(map)
    function clearMap(){
       
        start = false;
        marks = [];
        map.clearMap();
    }

    $('.poly').click(drawPolygon);
    $('.clearMap').click(clearMap);
    function drawPolyline () {
      mouseTool.polyline({
        strokeColor: "#3366FF", 
        strokeOpacity: 1,
        strokeWeight: 6,
        // 线样式还支持 'dashed'
        strokeStyle: "solid",
        // strokeStyle是dashed时有效
        // strokeDasharray: [10, 5],
      })
    }

    function drawPolygon () {
         start = true;
      mouseTool.polygon({
        strokeColor: "#FF33FF", 
        strokeOpacity: 1,
        strokeWeight: 6,
        strokeOpacity: 0.2,
        fillColor: '#1791fc',
        fillOpacity: 0.4,
        // 线样式还支持 'dashed'
        strokeStyle: "solid",
        // strokeStyle是dashed时有效
        // strokeDasharray: [30,10],
      })
    }

    function drawRectangle () {
       
      mouseTool.rectangle({
        strokeColor:'red',
        strokeOpacity:0.5,
        strokeWeight: 6,
        fillColor:'blue',
        fillOpacity:0.5,
        // strokeStyle还支持 solid
        strokeStyle: 'solid',
        // strokeDasharray: [30,10],
      })
    }

    function drawCircle () {
      mouseTool.circle({
        strokeColor: "#FF33FF",
        strokeOpacity: 1,
        strokeWeight: 6,
        strokeOpacity: 0.2,
        fillColor: '#1791fc',
        fillOpacity: 0.4,
        strokeStyle: 'solid',
        // 线样式还支持 'dashed'
        // strokeDasharray: [30,10],
      })
    }

    mouseTool.on('draw', function(event) {
        start = false;
      // event.obj 为绘制出来的覆盖物对象
      console.log(marks.join(','));
      $("input[name='{$this->id['id']}']").val(marks.join(', '));

      log.info('覆盖物对象绘制完成')
    })
});
EOT;
$css = "https://a.amap.com/jsapi_demos/static/demo-center/css/demo-center.css";
\Admin::css($css);
    }
}
