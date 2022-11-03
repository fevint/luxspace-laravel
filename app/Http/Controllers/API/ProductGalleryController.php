<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\ProductGallery;
use Illuminate\Http\Request;

class ProductGalleryController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $products_id = $request->input('products_id');
        $url = $request->input('url');
        $is_featured = $request->input('is_featured');

        if ($id) {
            $details = ProductGallery::with(['products'])->find($id);

            if ($details) {
                return ResponseFormatter::success(
                    $details,
                    'Data Product Gallery berhasil diambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data Product Gallery tidak ada',
                    404
                );
            }
        }
        $details = ProductGallery::query();

        if ($products_id) {
            $details->where('products_id', $products_id);
        }
        if ($url) {
            $details->where('products');
        }
       

        return ResponseFormatter::success(
            $details->paginate(),
            'Data Product Gallery berhasil diambil'
        );
    }
}
