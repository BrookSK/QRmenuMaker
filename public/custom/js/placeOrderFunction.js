function placeOrder(itemId, callback) {
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // Dados específicos para o pedido
    var requestData = {
        issd: true,
        vendor_id: "#modalID", // Altere para o valor real que deseja enviar
        delivery_method: "delivery",
        payment_method: "cod",
    };

    $.ajax({
        type: "POST",
        url: "/order",
        data: {
            item_id: itemId,
            requestData: requestData,
        },
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        success: function(response) {
            console.log("Pedido feito com sucesso!");
            // Execute a função de retorno de chamada e passe os dados relevantes
            if (callback && typeof callback === 'function') {
                callback(response, requestData);
            }
        },
        error: function(xhr, status, error) {
            console.error("Erro ao fazer o pedido:", error);
            // Adicione lógica de tratamento de erro, se necessário
        }
    });
}
