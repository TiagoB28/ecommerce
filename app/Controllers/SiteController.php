<?php

namespace App\Controllers;

use App\Models\{
    Category,
    Product,
    User,
    Cart
};

use Illuminate\Support\Facades\Session;
use Respect\Validation\Validator as v;


class SiteController extends Controller
{
    public function home($request, $response)
    {
        $categories = Category::all();
        $products = Product::all();

        $data = [
            'categories' => $categories,
            'products' => $products,
        ];

        return $this->container->view->render($response, 'site/home.twig', $data);
    }


    public function login($request, $response)
    {
        if($request->isGet())
            return $this->container->view->render($response, 'site/login.twig');




    }

    public function getCategoryProducts($request, $response)
    {
        $category = Category::find($request->getParam('id'));
        $products = Product::find($request->getParam('id'));

        $data = [
            'category' => $category,
            'products' => $products,
        ];

        return $this->container->view->render($response, 'site/category.twig', $data);
    }


    public function getProductDetail($request, $response)
    {
        $product = Product::find($request->getParam('id'));

        $data = [
            'product' => $product,
        ];

        return $this->container->view->render($response, 'site/product-detail.twig', $data);
    }


    public function getCartSession($request, $response, $args)
    {
        /*$cart = Cart::all();
        json($cart);*/

        /**
         * Se a sessão existe e o idcart for maior que 0, significa que o carrinho já
         * foi inserido no banco e que ele já está na sessão.
         */
        /*if (isset($_SESSION[Cart::SESSION]) && (int)$_SESSION[Cart::SESSION]['idcart'] > 0) {
            $cart = Cart::where('idcart', '=');
        } else {
            # code
        }*/



        return $this->container->view->render($response, 'site/cart.twig');
    }

    public function getCart(int $idcart)
    {
        $cart = Cart::where('idcart', '=', $idcart)->first();
        return $cart;
    }
}
