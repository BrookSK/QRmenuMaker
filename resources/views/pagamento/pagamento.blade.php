<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<center>
<h1>Pagamento</h1>
<h3>R$ {{ $preco }}</h3>
<p id="contador">Prazo para pagamento: 10:00</p>
<img src="data:image/png;base64,{{ $Qrcode }}">
<br>
<a href="https://{{ $BaseUri }}/restaurant/{{ $Subdomain }}" class="btn btn-danger">Cancelar pedido</a>
<br>
<br><br>
</center>
<div id="conteudo"></div>

<script>
// Função para carregar o conteúdo da div usando AJAX
function carregarConteudo() {
    // Faz uma requisição AJAX para seu_script.php
    $.ajax({
        url: 'https://{{ $BaseUri }}/status-pix/{{$idPedido}}',
        type: 'GET',
        success: function(response) {
            // Quando a requisição for bem-sucedida, insere o conteúdo dentro da div
            $('#conteudo').html(response);
        },
        error: function(xhr, status, error) {
            // Em caso de erro, exibe uma mensagem de erro no console do navegador
            console.error('Erro ao carregar conteúdo:', status, error);
        }
    });
}

$(document).ready(function() {
    setInterval(carregarConteudo, 5000);
});
</script>

<script>
// Definindo o tempo inicial em segundos (5 minutos)
var tempoInicial = 5 * 60;

// Função para formatar o tempo em minutos e segundos
function formatarTempo(segundos) {
  var minutos = Math.floor(segundos / 60);
  var segundosRestantes = segundos % 60;
  return minutos + ':' + (segundosRestantes < 10 ? '0' : '') + segundosRestantes;
}

// Função para atualizar o contador
function atualizarContador() {
  document.getElementById('contador').textContent = 'Prazo para pagamento: ' + formatarTempo(tempoInicial);
  if (tempoInicial === 0) {
    window.location.href = "https://{{ $BaseUri }}/restaurant/{{ $Subdomain }}"; // Redireciona para o URL desejado
  } else {
    tempoInicial--;
    setTimeout(atualizarContador, 1000); // Atualiza o contador a cada segundo
  }
}

// Iniciar o contador quando a página carregar
window.onload = function() {
  atualizarContador();
};
</script>