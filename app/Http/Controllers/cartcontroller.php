<?php
namespace App\Http\Controllers;

class CartController extends Controller {
    public function add($id){
        $cart = session()->get('cart',[]);
        $cart[] = $id;
        session(['cart'=>$cart]);
        return back();
    }

    public function index(){
        return view('cart');
    }
}
