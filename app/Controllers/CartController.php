<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use Carbon\Carbon;
use FlyingLuscas\Correios\Client;
use FlyingLuscas\Correios\Service;
use Illuminate\Database\Eloquent\Model;

class CartController extends Controller
{
    /**
     * Tentar pegar o cart pela session, se não existir cria um novo.
     * @return Cart
     */
    public function getFromSession()
    {
        if (!empty($this->getFromSessionID())) {
            $cart = $this->getFromSessionID(); // Tenta pegar o carrinho pela session
            if ($_SESSION['user'] > 0)
                $cart->update([
                    # Notice: Undefined index: user (quando deslogado)
                    'iduser' => $_SESSION['user']
                ]);

        } else {

            // Se carrinho existe, cria um novo.
            $cart = new Cart;
            $cart->dessessionid = session_id();
            $cart->save();
            if ($_SESSION['user'] > 0)
                $cart->update([
                    'iduser' => $_SESSION['user']
                ]);

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
        # Notice: Undefined index: Cart (quando deslogado e logado).
        # O Notice sai quando acrescentar um produto no Cart.
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
            ->whereNull('dtremoved') // ?
            ->get();

        $data = [
            'cartProducts' => $cartProducts
        ];

        if ($request->isGet())
            return $this->container->view->render($response, 'site/cart.twig', $data);

        if ($request->isPost())

            $cartProductsArray  = array();
            foreach ($cartProducts as $key => $cartProduct) {
                $cartProductsArray[$key] = [
                    'vlwidth' => $cartProduct->product->vlwidth,
                    'vlheight' => $cartProduct->product->vlheight,
                    'vllength' => $cartProduct->product->vllength,
                    'vlweight' => $cartProduct->product->vlweight,
                    'qtd' => $cartProduct->qtd,
                ];
            }

            $zipcode = str_replace('-', '', $request->getParam('zipcode'));

            $carrierPigeon = new Client;
            $carrierPigeon = $carrierPigeon
                ->freight()
                ->origin('01001-000')
                ->destination($zipcode)
                ->services(Service::SEDEX);


            foreach ($cartProductsArray as $array) {

                if( $array['vlwidth'] > 11) {
                    $width = $array['vlwidth'];
                } else {
                    $width = 11;
                }

                if( $array['vlheight'] > 11) {
                    $height = $array['vlheight'];
                } else {
                    $height = 11;
                }

                if( $array['vllength'] >= 16) {
                    $length = $array['vllength'];
                } else {
                    $length = 16;
                }

                $weight = $array['vlweight'];

                $qtd = $array['qtd'];

                # largura, altura, comprimento, peso e quantidade
                $carrierPigeon->item($width, $height, $length, $weight, $qtd);
            }

            $pigeons = $carrierPigeon->calculate();
            $pigeonPrice = $pigeons[0]['price'];
            $pigeonDeadline = $pigeons[0]['deadline'];

            $data = [
                'cartProducts' => $cartProducts,
                'pigeonPrice' => $pigeonPrice,
                'pigeonDeadline' => $pigeonDeadline,
            ];

            $cart->deszipcode = $request->getParam('zipcode');
            $cart->vlfreight = $pigeonPrice;
            $cart->nrdays = $pigeonDeadline;
            $cart->save();

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
