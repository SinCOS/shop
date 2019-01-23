<?php

use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 创建 30 个商品
        $users = factory(\App\Models\User::class, 30)->create();
        $category = \App\Models\Category::where('parent_id' ,'>',0)->pluck('id');
        var_dump($category);
        foreach ($users as $user) {
            $model = factory(\App\Models\Shop::class,1)->create(['user_id' => $user->id,
            'cat_id' => $category[random_int(0,count($category))],            
            ]);
           // $user->shop_id = $model->id;
            //user->save();
            \DB::table("admin_role_users")->insert(['role_id'=>2,'user_id'=>$user->id]);
            // 创建 3 个 SKU，并且每个 SKU 的 `product_id` 字段都设为当前循环的商品 id
            // $skus = factory(\App\Models\ProductSku::class, 3)->create(['product_id' => $product->id]);
            // // 找出价格最低的 SKU 价格，把商品价格设置为该价格
            // $product->update(['price' => $skus->min('price')]);
        }
    }
}
