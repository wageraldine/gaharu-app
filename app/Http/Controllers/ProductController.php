<?php

namespace App\Http\Controllers;

class ProductController extends Controller
{
    public function index()
    {
        $products = [
            [
                'name' => 'Gaharu Kalimantan',
                'price' => 'Rp 5.000.000',
                'image' => 'gaharu1.jpg',
                'desc' => 'Kualitas super dari hutan Kalimantan'
            ],
            [
                'name' => 'Gaharu Sumatra',
                'price' => 'Rp 4.500.000',
                'image' => 'gaharu2.jpg',
                'desc' => 'Aroma tajam dan tahan lama'
            ],
            [
                'name' => 'Gaharu Merauke',
                'price' => 'Rp 6.000.000',
                'image' => 'gaharu3.jpg',
                'desc' => 'Langka dan eksotis'
            ]
        ];

        return view('products', compact('products'));
    }
}
