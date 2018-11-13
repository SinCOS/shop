<?php

namespace App\Admin\Controllers;
use App\Http\Controllers\Controller;
function random($length, $numeric = FALSE) {
   	$seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
	$seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
	if ($numeric) {
		$hash = '';
	} else {
		$hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
		$length--;
	}
	$max = strlen($seed) - 1;
	for ($i = 0; $i < $length; $i++) {
		$hash .= $seed{mt_rand(0, $max)};
	}
	return $hash;
}
/**
 * 
 */
class TplController extends Controller
{
	
	public function show(){
		$tpl = request()->input('tpl');
		if($tpl == 'option'){
			$tag = random(32);
			return view('admin.tpl.sku_option',['tag' => $tag]);
		}elseif ($tpl == 'spec') {
			$spec = [
				'id' => random(32),
				'title' => request()->input('title','')
			];
			return view("admin.tpl.sku_spec",['spec' => $spec]);
		}elseif ($tpl == 'specitem') {
			$spec = [
				'id' => request()->input('specid')
			];
			$specitem = [
				'id' => random(32),
				'title' => request()->input('title',''),
				'show' => 1
			];
			return view('admin.tpl.sku_spec_item',['spec' => $spec,'specitem' => $specitem]);
		}
	}
}