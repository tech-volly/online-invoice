<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $category = Category::whereName($row['product_category'])->first();
        $product_price_res = getCalculatedPrice($row['product_tax_type'], $row['product_price']);
     
        return new Product([
            'product_name' => $row['product_name'],
            'product_slug' => replaceSpaceWithDash($row['product_name']),
            'category_id' => $category->id,
            'product_tax' => $row['product_tax_type'],
            'product_price' => $row['product_price'],
            'product_purchase_price' => $row['product_purchase_price'],
            'product_margin' => $row['product_price'] - $row['product_purchase_price'],
            'product_base_price' => $product_price_res['product_base_price'],
            'product_gst_value' => $product_price_res['product_gst_value'],
            'product_final_price' => $product_price_res['product_final_price'],
            'product_description' => $row['product_description'],
            'is_status' => 1
        ]);
    }
}
