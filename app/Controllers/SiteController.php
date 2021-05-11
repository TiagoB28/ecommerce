<?php

namespace App\Controllers;

use App\Models\{Category, Person, Product, User, Cart};
use Respect\Validation\Validator as v;

class SiteController extends Controller
{

    public function home($request, $response)
    {
        if ($_SESSION['user'] > 0) {
            $user_name = User::find($_SESSION['user'])->deslogin;
        } else {
            $user_name = null;
        }
        $categories = Category::all();
        $products = Product::all();

//        json($_SESSION['user']);

        $data = [
            'user' => $_SESSION['user'],
            'user_name' => $user_name,
            'categories' => $categories,
            'products' => $products
        ];

        return $this->container->view->render($response, 'site/home.twig', $data);
    }

    public function createUserSite($request, $response)
    {
        $validation = $this->container->validator->validate($request,[
            'desperson' =>  v::notEmpty()->alpha()->length(5),
            'deslogin' =>  v::notEmpty()->noWhitespace()->email(),
            'nrphone' => v::notEmpty()->noWhitespace()->regex("/^\(\d{2}\)\d{4}-\d{4}$/"),
            'desemail' => v::notEmpty()->noWhitespace()->email(),
            'despassword' => v::notEmpty()->noWhitespace()
        ]);

        if($validation->failed())
            $response->withRedirect($this->container->router->pathFor('site.login'));


        Person::create([
            'desperson' => $request->getParam('name'),
            'desemail' => $request->getParam('email'),
            'nrphone' => $request->getParam('phone')
        ])
            ->user()
            ->create([
                'deslogin' => $request->getParam('email'),
                'despassword' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
                'inadmin' => 0
            ]);
        $this->container->flash->addMessage('success', 'Conta Criada.');
        return $response->withRedirect($this->container->router->pathFor('site.login'));
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
