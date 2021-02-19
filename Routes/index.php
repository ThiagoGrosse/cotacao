<?php

use App\Controllers\Downloads;
use App\Controllers\OrderQuotation;
use App\Controllers\ProductsController;
use App\Controllers\Quotation;
use App\Controllers\ShippingCompanyController;
use App\Middlewares\AuthConnection;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use function src\slimConfiguration;

$app = new \Slim\App(slimConfiguration());

$app->add(function ($request, $response, $next) {
    $response = $next($request, $response);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization, token, FormData')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST')
        ->withHeader('Access-Control-Allow-Credentials', false);
});

$app->group('/api/v1', function () use ($app) {

    /**
     * TRANSPORTADORAS
     */

    // Busca transportadoras ( TUDO OU POR ID )
    $app->get('/transportadoras[/{id}]', ShippingCompanyController::class . ':getShippingCompany');

    // Atualiza a transportadora
    $app->post('/transportadoras', ShippingCompanyController::class . ':updateShippingCompany');


    /**
     * PRODUTOS
     */
    //Busca todos os produtos
    $app->get('/produtos', ProductsController::class . ':getAllProducts');


    /**
     * COTAÇÕES
     */

    // Cotação simples
    $app->post('/cotacao', Quotation::class . ':simpleQuotation');

    // Simula produto
    $app->post('/simulaProduto', Quotation::class . ':simulateProduct');
})->add(AuthConnection::class . ':validateConnection');


$app->post('/teste', function (Request $request, Response $response) {
    die(json_encode('aqui'));
});


/**
 * Grupo de cotação massiva
 */
$app->group('/api/v1/massivo', function () use ($app) {

    // Cotação Massiva de Pedidos
    $app->post('/cotacaoMassivaPedidos', OrderQuotation::class . ':orderListQuote');

    // Cotação Massiva
    $app->post('/cotacaoMassiva', Quotation::class . ':massiveQuotation');
});


/**
 * DOWNLOADS
 */

// Atualiza Preço
$app->get('/precoDownload', Downloads::class . ':downloadPrice');

// Atualiza Prazo
$app->get('/prazoDownload', Downloads::class . ':downloadDeadline');

// Atualiza SKU
$app->get('/skuDownload', Downloads::class . ':downloadSku');


$app->run();
