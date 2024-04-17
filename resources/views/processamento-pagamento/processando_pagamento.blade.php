<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processamento de Pagamento</title>
</head>
<body>
    <h1>Processando Pagamento</h1>
    <p>Obrigado por fazer o seu pedido! Aguarde enquanto processamos o seu pagamento.</p>

    <!-- Se estiver passando o ID do pedido como parâmetro de rota -->
    <p>ID do Pedido: {{ $order->id }}</p>

    <!-- Se estiver passando o ID do pedido como parâmetro de consulta -->
    <!-- <p>ID do Pedido: {{ request()->query('order_id') }}</p> -->
</body>
</html>
