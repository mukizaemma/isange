<?php

namespace App;

use Illuminate\Support\Facades\Session;

class Cart
{
    public function __construct()
    {
        if (!Session::has('cart')) {
            Session::put('cart', []);
        }
    }

    public function add($id, $name, $price, $qty)
    {
        $cart = Session::get('cart');
        if (isset($cart[$id])) {
            $cart[$id]['qty'] += $qty;
        } else {
            $cart[$id] = [
                'name' => $name,
                'price' => $price,
                'qty' => $qty,
            ];
        }
        Session::put('cart', $cart);
    }

    public function remove($id)
    {
        $cart = Session::get('cart');
        unset($cart[$id]);
        Session::put('cart', $cart);
    }

    public function update($id, $qty)
    {
        $cart = Session::get('cart');
        $cart[$id]['qty'] = $qty;
        Session::put('cart', $cart);
    }

    public function clear()
    {
        Session::forget('cart');
    }

    public function all()
    {
        return Session::get('cart');
    }

    public function count()
    {
        return count(Session::get('cart'));
    }

    public function total()
    {
        $cart = Session::get('cart');
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}
