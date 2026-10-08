<?php
require __DIR__ . '/util.php';
exigir_post();
exigir_campos(['sku', 'nome', 'categoria', 'fornecedor', 'preco_custo', 'preco_venda', 'estoque_minimo', 'unidade']);

$sku = strtoupper(campo('sku'));
$custo = numero('preco_custo');
$venda = numero('preco_venda');

if (!preg_match('/^[A-Z0-9-]{3,20}$/', $sku)) {
    responder(false, 'O SKU deve ter de 3 a 20 caracteres, só letras, números e hífen.');
}
if (!is_numeric(str_replace(',', '.', campo('preco_custo'))) || $custo <= 0) {
    responder(false, 'O preço de custo deve ser maior que zero.');
}
if (!is_numeric(str_replace(',', '.', campo('preco_venda'))) || $venda <= 0) {
    responder(false, 'O preço de venda deve ser maior que zero.');
}
if (!ctype_digit(campo('estoque_minimo'))) {
    responder(false, 'O estoque mínimo deve ser um número inteiro maior ou igual a zero.');
}
if (!in_array(campo('unidade'), ['un', 'cx', 'kit'], true)) {
    responder(false, 'Unidade inválida.');
}
if ($venda <= $custo) {
    responder(false, 'O preço de venda deve ser maior que o preço de custo.');
}

// Lógica adicional: calcula lucro e margem do produto.
$lucro = $venda - $custo;
$margem = ($lucro / $venda) * 100;

responder(true, 'Produto validado com sucesso.', [
    'sku' => $sku,
    'produto' => campo('nome'),
    'lucro_por_unidade' => reais($lucro),
    'margem' => number_format($margem, 1, ',', '.') . '%',
]);
