<?php
namespace App\Http\Wrapper;
use Admin;
use App\Admin\Extensions\Form\CityPicker;
use Encore\Admin\Form;
use Encore\Admin\Layout\Content;


class UserContent extends Content
{
    protected $view = 'admin';

    public function __construct(\Closure $callback = null)
    {
        Form::registerBuiltinFields();
        // Form::extend('city_picker', CityPicker::class);
        $assets = Form::collectFieldAssets();
        Admin::css($assets['css']);
        Admin::js($assets['js']);
        parent::__construct($callback);
    }

    public function render()
    {
        $items = [
            'title' => $this->header . ' ' . $this->description,
            'header' => $this->header,
            'description' => $this->description,
            'breadcrumb' => $this->breadcrumb,
            'content' => $this->build(),
        ];

        return view($this->view, $items);
    }

}