// Envio dos formulários via fetch (sem recarregar a página).
// Cada <form> informa o arquivo PHP de destino no atributo data-endpoint.
const formulario = document.getElementById("formulario");
const caixaMensagem = document.getElementById("mensagem");

function mostrarMensagem(tipo, titulo, dados) {
  caixaMensagem.className = tipo;
  caixaMensagem.innerHTML = "";
  const forte = document.createElement("strong");
  forte.textContent = titulo;
  caixaMensagem.appendChild(forte);
  const entradas = Object.entries(dados || {});
  if (entradas.length > 0) {
    const lista = document.createElement("ul");
    entradas.forEach(([chave, valor]) => {
      const item = document.createElement("li");
      item.textContent = chave.replace(/_/g, " ") + ": " + valor;
      lista.appendChild(item);
    });
    caixaMensagem.appendChild(lista);
  }
}

function validarCamposVazios() {
  let primeiroVazio = null;
  formulario.querySelectorAll("input, select, textarea").forEach((campo) => {
    const vazio = campo.value.trim() === "";
    campo.classList.toggle("erro", vazio);
    if (vazio && !primeiroVazio) primeiroVazio = campo;
  });
  if (primeiroVazio) primeiroVazio.focus();
  return primeiroVazio === null;
}

formulario.addEventListener("submit", async (evento) => {
  evento.preventDefault();

  if (!validarCamposVazios()) {
    mostrarMensagem("falha", "Preencha todos os campos destacados.");
    return;
  }

  const botao = formulario.querySelector("button");
  botao.disabled = true;

  try {
    const resposta = await fetch(formulario.dataset.endpoint, {
      method: "POST",
      body: new FormData(formulario),
    });
    const resultado = await resposta.json();
    if (resultado.ok) {
      const tipo = resultado.dados && resultado.dados.alerta ? "aviso" : "ok";
      mostrarMensagem(tipo, resultado.mensagem, resultado.dados);
      formulario.reset();
    } else {
      mostrarMensagem("falha", resultado.mensagem);
    }
  } catch (erro) {
    mostrarMensagem("falha", "Não foi possível falar com o servidor. Verifique se o PHP está rodando.");
  } finally {
    botao.disabled = false;
  }
});
