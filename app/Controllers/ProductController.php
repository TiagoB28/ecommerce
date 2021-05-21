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


    /**
     * Criar produto
     * @param $request
     * @param $response
     * @return mixed
     * @throws \Exception
     */
    public function createProduct($request, $response)
    {
        if ($request->isGet())
            return $this->container->view->render($response, 'admin/products-create.twig');

//        $directory = $this->container->get('upload_directory');

        $files = $request->getUploadedFiles();

        $newFile = $files['desphoto'];
        if ($newFile->getError() === UPLOAD_ERR_OK)
            $uploadFileName = $newFile->getClientFilename();
            $newFileName = bin2hex(random_bytes(3)) . '_' . $uploadFileName;
            $newFile->moveTo($this->container->get('upload_directory') . DIRECTORY_SEPARATOR . $newFileName);


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
            'desimage' => $newFileName
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
     * Update do produto
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

        if ($request->ispost())
            $files = $request->getUploadedFiles();

            if (empty($newFile = $files['desphoto'])) {
                $newFileName = $product->desimage;
            } else {
                $newFile = $files['desphoto'];
                if ($newFile->getError() === UPLOAD_ERR_OK)
                    $uploadFileName = $newFile->getClientFilename();
                    $newFileName = bin2hex(random_bytes(3)) . '_' . $uploadFileName;
                    $newFile->moveTo($this->container->get('upload_directory') . DIRECTORY_SEPARATOR . $newFileName);
            }

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
            'desimage' => $newFileName
        ]);

        $this->container->flash->addMessage('success', 'Usuário atualizado com sucesso!');

        return $response->withRedirect($this->container->router->pathFor('admin.products'));
    }

    /**
     * Deleta produto
     * @param $request
     * @param $response
     * @return mixed
     */
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