<?php

use App\Middleware\AuthMiddleware;

$app->group('/site', function ($app) {
    $app->map(['GET', 'POST'], '/login', 'SiteController:login')->setName('site.login');
    $app->get('/home', 'SiteController:home')->setName('site.home');
    $app->get('/category', 'SiteController:getCategoryProducts')->setName('site.category');
    $app->get('/product_detail', 'SiteController:getProductDetail')->setName('site.product-detail');

    $app->get('/cart', 'SiteController:getCartSession')->setName('site.cart');
});

$app->group('/admin', function ($app) {
    $app->get('/home', 'AdminController:index')->setName('admin.home');
    $app->get('/users', 'AdminController:getUsers')->setName('admin.users');
    $app->map(['GET', 'POST'], '/create', 'AdminController:createUser')->setName('admin.user-create');
    $app->map(['GET', 'POST'], '/user/update/{id}', 'AdminController:updateUser')->setName('admin.user-update');
    $app->get('/user/delete', 'AdminController:deleteUser');

    $app->get('/categories', 'CategoryController:getCategories')->setName('admin.categories');
    $app->map(['GET', 'POST'], '/category/create', 'CategoryController:createCategory')->setName('admin.category-create');
    $app->map(['GET', 'POST'], '/category/update/{id}', 'CategoryController:updateCategory')->setName('admin.category-update');
    $app->get('/category/delete', 'CategoryController:deleteCategory');

    $app->get('/categories/products/{id}', 'CategoryController:getProductCategory')->setName('admin.categories-products');

    /**
     * Products VS Categories
     */
    $app->get('/categories/{id_cart}/products/{id_product}/add', 'CategoryController:addProductCategory')->setName('admin.categories-add');
    $app->get('/categories/{id_cart}/products/{id_product}/remove', 'CategoryController:removeProductCategory')->setName('admin.categories-remove');


    $app->get('/products', 'ProductController:getProducts')->setName('admin.products');
    $app->map(['GET', 'POST'], '/product/create', 'ProductController:createProduct')->setName('admin.product-create');
    $app->map(['GET', 'POST'], '/product/update/{id}', 'ProductController:updateProduct')->setName('admin.product-update');
    $app->get('/product/delete', 'ProductController:deleteProduct');
})->add(new AuthMiddleware($container));


$app->group('/auth', function ($app) {
    $app->map(['GET', 'POST'],'/login', 'AuthController:login')->setName('auth.login');
    $app->map(['GET', 'POST'], '/forgot', 'AuthController:forgot')->setName('auth.forgot');
    $app->get('/logout', 'AuthController:logout')->setName('auth.logout');
});
















/*$app->group('/usuario', function ($app) {
    $app->map(['GET', 'POST'], '/avatar', 'UserController:avatar')->setName('user.avatar');
});


$app->group('/postagem', function ($app) {
    $app->map(['GET', 'POST'], '/criar', 'PostController:create')->setName('post.create');

    $app->get('/deletar', 'PostController:delete')->setName('post.delete');

    $app->get('/edit/{id}', 'PostController:edit')->setName('post.edit');
    $app->post('/edit/{id}', 'PostController:update');
})->add(new AuthMiddleware($container));


$app->group('/auth', function($app) {
    $app->map(['GET', 'POST'], '/login', 'AuthController:login')->setName('auth.login');
    $app->map(['GET', 'POST'], '/registrar', 'AuthController:register')->setName('auth.register');
    $app->get('/confirmacao', 'AuthController:confirmation');
    $app->get('/reenviar', 'AuthController:resend')->setName('auth.resend');
    $app->get('/logout', 'AuthController:logout')->setName('auth.logout');
});*/
