<?php

namespace App\Controllers;

use App\Models\{Category, Person, Product, User, Cart, CartProducts};
use Respect\Validation\Validator as v;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class SiteController extends Controller
{

    public function home($request, $response)
    {
        $categories = Category::all();
        $products = Product::all();

        $data = [
            'categories' => $categories,
            'products' => $products
        ];

        return $this->container->view->render($response, 'site/home.twig', $data);
    }


    public function createUserSite($request, $response)
    {
        $validation = $this->container->validator->validate($request, [
            'desperson' => v::notEmpty()->alpha()->length(5),
            'deslogin' => v::notEmpty()->noWhitespace()->email(),
            'nrphone' => v::notEmpty()->noWhitespace()->regex("/^\(\d{2}\)\d{4}-\d{4}$/"),
            'desemail' => v::notEmpty()->noWhitespace()->email(),
            'despassword' => v::notEmpty()->noWhitespace()
        ]);

        if ($validation->failed())
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


    public function getCategoryProducts($request, $response, $args)
    {
        $id = $request->getParam('id');
        $page = $request->getParam('page');

        Paginator::currentPathResolver(function() use($page) { return $page; });
        Paginator::currentPageResolver(function() use($page) { return $page; });

        $category = Category::find($id);
        $categories = Category::all();

        $productsRelated = Product::query()
            ->whereHas('categories', function ($q) use ($id, $args) {
            $q->where('tb_productscategories.idcategory', '=', $id);
        })->paginate(2);

        $numberOfPages = $productsRelated->lastPage();  // retorna o número da última página
        $nextPageUrl = $productsRelated->nextPageUrl();  // "hcode-slim-3/public/site/category?id=2&page=2"
        $itemsPerPage = $productsRelated->items();  // pega os items da página
        $onFirstPage = $productsRelated->onFirstPage(); // retorna true se for a primeira página

        $data = [
            'id' => $id,
            'categories' => $categories,
            'category' => $category,
            'productsRelated' => $productsRelated,
            'numberOfPages' => $numberOfPages,
            'nextPageUrl' => $nextPageUrl,
            'onFirstPage' => $onFirstPage
        ];

        return $this->container->view->render($response, 'site/category.twig', $data);
    }


    public function getProductDetail($request, $response)
    {
        $product = Product::find($request->getParam('id'));
        $category = Category::find($request->getParam('id'));

        $data = [
            'product' => $product,
            'category' => $category
        ];

        return $this->container->view->render($response, 'site/product-detail.twig', $data);
    }



    /*public function getCart($request, $response)
    {
        $categories = Category::all();
        $product = Product::find($request->getParam('id'));

        $data = [
            'categories' => $categories,
            'product' => $product
        ];

        return $this->container->view->render($response, 'site/cart.twig', $data);
    }*/


    /**
     * Tentar pegar o cart pela session, se não existir cria um novo.
     * @return Cart
     */
    public function getFromSession()
    {
        $cart = $this->getFromSessionID(); // Tenta pegar o carrinho pela session

        // se carrinho existe, cria um novo.
        if (empty($cart)) {

            $cart = new Cart;
            $cart->dessessionid = session_id();
            $cart->save();

            // Salvar carrinho na Session
            $_SESSION[Cart::SESSION] = $cart->getAttributes();
        }

        return $cart;
    }


    /**
     * Se a sessão existe e o idcart for maior que 0, significa que o carrinho já
     * foi inserido no banco e que ele já está na sessão.
     * @return mixed
     */
    private function getFromSessionID()
    {

        $cart_session = $_SESSION[Cart::SESSION];

        if (isset($cart_session) && (int)$cart_session['idcart'] > 0) {
            $cart = Cart::find($cart_session['idcart']);
        }else{
            $cart =  $cart = Cart::where('idcart', '=', session_id())->first();
        }

        return $cart;
    }


    public function getCartProducts($request, $response)
    {
        $cart = $this->getFromSession();

        $cartProducts = CartProducts::with('product')
            ->where('idcart', '=', $cart->idcart)
            ->whereNull('dtremoved')
            ->get();

        $data = [
            'cartProducts' => $cartProducts
        ];

        return $this->container->view->render($response, 'site/cart.twig', $data);
    }


    public function addProduct($request, $response, $args)
    {
        $cart = $this->getFromSession();

        $cartProduct = CartProducts::firstOrNew([
            'idcart' =>  $cart->idcart,
            'idproduct' => $args['idproduct']
        ]);

        $cartProduct->qtd += 1;
        $cartProduct->save();

        return $response->withRedirect($this->container->router->pathFor('site.cart'));
    }


    public function minusProduct($request, $response, $args)
    {
        $cart = $this->getFromSession();

        $cartProduct = CartProducts::firstOrNew([
            'idcart' =>  $cart->idcart,
            'idproduct' => $args['idproduct']
        ]);

        if ($cartProduct->qtd > 1){
            $cartProduct->qtd -= 1;
            $cartProduct->save();
        }

        return $response->withRedirect($this->container->router->pathFor('site.cart'));
    }

    public function removeProduct($request, $response, $args){
        $cart = $this->getFromSession();

        $cartProduct = CartProducts::where('idcart','=',$cart->idcart)
            ->where('idproduct', '=', $args['idproduct'])
            ->whereNotNull('dtremoved')
            ->first();

        $cartProduct->dtremoved = Carbon::now();
        $cartProduct->save();

        return $response->withRedirect($this->container->router->pathFor('site.cart'));
    }
}
