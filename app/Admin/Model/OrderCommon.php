<?php
namespace App\Admin\Model;


class OrderCommon {
   // protected $db ;
    function __construct($table ='order')
    {
       // $this->db = \DB::table($table);
    }

    public static function getDays($day){
       
      $result =  \DB::table('orders')->select(\DB::raw(" count(*) as total ,date(paid_at) as dated "))->groupBy('dated')->get()->map(function ($value) {
            return (array)$value;
        })->toArray();
        $arr = array_column($result,'total', 'dated');
        $days = [];
        $arr2= [];
        for ($i=$day; $i >= 0; $i--) { 
           $dayed =  \Carbon\Carbon::parse("-{$i} days")->format('Y-m-d');
            $days[] = $dayed;
            if(!isset($arr[$dayed])){
                $arr2[] = 0;
            }else{
                $arr2[] = $arr[$dayed];
            }
        }
        return ['days' => $days,'vals' =>implode(',',$arr2)];
    }
}