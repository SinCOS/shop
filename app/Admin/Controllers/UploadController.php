<?php

namespace App\Admin\Controllers;

use App\Exceptions\UploadException;
use App\Http\Controllers\Controller;
use App\Services\UploadServe;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    /**
     * @param UploadServe $uploadServe
     * @return array
     */
    public function uploadByEditor(UploadServe $uploadServe)
    {
        $disk = 'admin';

        try {
            $files = $uploadServe->setFileInput('pictures')
                                 ->setMaxSize('10M')
                                 ->setExtensions(['jpg', 'jpeg', 'png', 'bmp', 'gif'])
                                 ->validate()
                                 ->storeMulti('upload/editor', compact('disk'));

            $files = collect($files)->map(function ($file) use ($disk) {
                return Storage::disk($disk)->url($file);
            })->all();


        } catch (UploadException $e) {

            return ['errno' => 1, 'msg' => $e->getMessage()];
        }

        return ['errno' => 0, 'data' => $files];
    }
    public function uploadByFileInput(UploadServe $uploadServe){
        $disk = 'admin';
        try {
               $file = $uploadServe->setFileInput('thumb')
                                 ->setMaxSize('10M')
                                 ->setExtensions(['jpg', 'jpeg', 'png', 'bmp', 'gif'])
                                 ->validate()
                                 ->storeMulti('products/lists/'.date('Y-m-d'), compact('disk'));
                                 // dd($file);
            $path =  Storage::disk($disk)->url($file[0]);
           
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'path' => NULL];
        }
        return ['error' => NULL, 'path' => $path];
    }
}
