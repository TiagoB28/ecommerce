<?php

namespace App\Controllers;

use App\Models\Product;

use Respect\Validation\Validator as v;

class ProductController extends Controller
{
    public function getProducts($request, $response)
    {
        $products = Product::all();

        $data = [
            'products' => $products,
        ];

        return $this->container->view->render($response, 'admin/products.twig', $data);
    }


    public function createProduct($request, $response)
    {
        if ($request->isGet())
            return $this->container->view->render($response, 'admin/products-create.twig');

        $directory = $this->container->get('upload_directory');

        $files = $request->getUploadedFiles();

        $newFile = $files['desphoto'];


        $extension = $newFile->getClientFilename();
        $uploadedFileName = bin2hex(random_bytes(5));

        $upload = sprintf('%s.%0.8s', $uploadedFileName, $extension);

        $newFile->moveTo($directory . DIRECTORY_SEPARATOR . $upload);

        $validation = $this->container->validator->validate($request, [
            'desproduct' => v::notEmpty()->alpha()->length(5),
        ]);

        if ($validation->failed())
            $response->withRedirect($this->container->router->pathFor('admin.product-create'));

        Product::create([
            'desproduct' => $request->getParam('desproduct'),
            'vlprice' => $request->getParam('vlprice'),
            'vlwidth' => $request->getParam('vlwidth'),
            'vlheight' => $request->getParam('vlheight'),
            'vllength' => $request->getParam('vllength'),
            'vlweight' => $request->getParam('vlweight'),
            'desimage' => $uploadedFileName
        ]);

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['desphoto'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $filename = $uploadedFiles->getClientFilename();
            $uploadedFile->moveTo(__DIR__ . '/public/images' . DIRECTORY_SEPARATOR . $filename);
        }

        return $response->withRedirect($this->container->router->pathFor('admin.products'));
    }


    /**
     * @param $request
     * @param $response
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function updateProduct($request, $response, $args)
    {
        $product = Product::where('idproduct', '=', $args['id'])->first();

        $data = [
            'product' => $product
        ];

        if ($request->isGet())
            return $this->container->view->render($response, 'admin/products-update.twig', $data);


        $directory = $this->container->get('upload_directory');

        $files = $request->getUploadedFiles();
        $newFile = $files['desphoto'];

        $extension = pathinfo($newFile->getClientFilename(), PATHINFO_EXTENSION);

        $uploadedFileName = bin2hex(random_bytes(5));

//        $upload = sprintf('%s', $uploadedFileName . '.' . $extension);
        $upload =  "{$uploadedFileName}.{$extension}";

        $newFile->moveTo($directory . DIRECTORY_SEPARATOR . $upload);


        $validation = $this->container->validator->validate($request, [
            'desproduct' => v::notEmpty()->alpha()->length(5),
        ]);

        if ($validation->failed())
            $response->withRedirect($this->container->router->pathFor('admin.product-create'));

        $product->update([
            'desproduct' => $request->getParam('desproduct'),
            'vlprice' => $request->getParam('vlprice'),
            'vlwidth' => $request->getParam('vlwidth'),
            'vlheight' => $request->getParam('vlheight'),
            'vllength' => $request->getParam('vllength'),
            'vlweight' => $request->getParam('vlweight'),
            'desimage' => $uploadedFileName
        ]);

        $this->container->flash->addMessage('success', 'Usuário atualizado com sucesso!');

        return $response->withRedirect($this->container->router->pathFor('admin.products'));
    }


    public function deleteProduct($request, $response)
    {
        $product = Product::find($request->getParam('id'));

        if ($product) {
            $product->delete();
            $this->container->flash->addMessage('success', 'Produto deletado');
        } else {
            $this->container->flash->addMessage('error', 'Produto não pode ser deletado');
        }

        return $response->withRedirect($this->container->router->pathFor('admin.products'));
    }
}