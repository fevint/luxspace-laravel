<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $price = $request->input('price');
        $description = $request->input('description');
        $slug = $request->input('slug');

        if ($id) {
            $product = Product::with(['galleries'])->find($id);

            if ($product) {
                return ResponseFormatter::success(
                    $product,
                    'Data product berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data product tidak ada',
                    404
                );
            }
        }
        $product = Product::with(['galleries']);

        if ($name) {
            $product->where('name', 'like', '%' . $name . '%');
        }
        if ($price) {
            $product->where('price', $price);
        }
        if ($description) {
            $product->where('description', 'like', '%' . $description . '%');
        }
        if ($slug) {
            $product->where('slug', 'like', '%' . $slug . '%');
        }
        return ResponseFormatter::success(
            $product->paginate(),
            'Data product berhasil diambil'
        );
    }
}
