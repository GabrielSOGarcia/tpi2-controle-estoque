<?php
require __DIR__ . '/util.php';
exigir_post();
exigir_campos(['sku', 'quantidade', 'estoque_atual', 'estoque_minimo', 'motivo', 'data_saida', 'responsavel']);

$sku = strtoupper(campo('sku'));

if (!preg_match('/^[A-Z0-9-]{3,20}$/', $sku)) {
    responder(false, 'SKU inválido.');
}
foreach (['quantidade', 'estoque_atual', 'estoque_minimo'] as $nome) {
    if (!ctype_digit(campo($nome))) {
        responder(false, "O campo $nome deve ser um número inteiro maior ou igual a zero.");
    }
}
if ((int) campo('quantidade') < 1) {
    responder(false, 'A quantidade retirada deve ser maior que zero.');
}
if (!in_array(campo('motivo'), ['venda', 'perda', 'devolucao'], true)) {
    responder(false, 'Motivo inválido.');
}
if (!data_valida(campo('data_saida')) || data_no_futuro(campo('data_saida'))) {
    responder(false, 'Data de saída inválida ou no futuro.');
}
if (tamanho(campo('responsavel')) < 3) {
    responder(false, 'Informe o nome do responsável.');
}

// Lógica adicional: impede saída maior que o estoque e avisa quando o saldo fica baixo.
$quantidade = (int) campo('quantidade');
$atual = (int) campo('estoque_atual');
$minimo = (int) campo('estoque_minimo');

if ($quantidade > $atual) {
    responder(false, "Estoque insuficiente: há $atual unidade(s) e você tentou retirar $quantidade.");
}

$saldo = $atual - $quantidade;
$dados = [
    'sku' => $sku,
    'quantidade_retirada' => $quantidade,
    'saldo_restante' => $saldo,
];

if ($saldo <= $minimo) {
    $dados['alerta'] = 'Estoque no mínimo ou abaixo dele. Providencie a reposição.';
    responder(true, 'Saída validada, mas atenção ao estoque.', $dados);
}

responder(true, 'Saída de estoque validada com sucesso.', $dados);
