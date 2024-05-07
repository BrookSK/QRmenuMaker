<center>
<h1>Pagamento</h1>
<h3>R$ 10.00</h3>
<p id="contador">Prazo para pagamento: 10:00</p>
<img src="data:image/png;base64,{{ $Qrcode }}">
</center>
<script>
// Definindo o tempo inicial em segundos (10 minutos)
var tempoInicial = 10 * 60;

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
    window.location.href = "https://beta.onsolutionsbrasil.com.br/restaurant/testelrvweb"; // Redireciona para o URL desejado
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