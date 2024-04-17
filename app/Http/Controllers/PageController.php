<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order; // Certifique-se de importar o modelo Order, se necessário

class PageController extends Controller
{
    /**
     * Exibe a página de processamento de pagamento.
     *
     * @param  int  $order_id
     * @return \Illuminate\View\View
     */
    public function processandoPagamentoAsaas($order_id = null)
    {
        // Se você optou por passar o ID do pedido como parâmetro de rota
        $order = Order::find($order_id);

        // Se você optou por passar o ID do pedido como parâmetro de consulta
        // $order_id = request()->query('order_id');
        // $order = Order::find($order_id);

        // Aqui você pode fazer qualquer lógica necessária e depois retornar a visualização da página
        return view('processamento-pagamento.processando_pagamento', ['order' => $order]);
    }
}
