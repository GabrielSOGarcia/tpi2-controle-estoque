<?php
require __DIR__ . '/util.php';
exigir_post();
exigir_campos(['sku', 'fornecedor', 'quantidade', 'custo_unitario', 'data_entrada', 'nota_fiscal']);

$sku = strtoupper(campo('sku'));
$custo = numero('custo_unitario');

if (!preg_match('/^[A-Z0-9-]{3,20}$/', $sku)) {
    responder(false, 'SKU inválido.');
}
if (!ctype_digit(campo('quantidade')) || (int) campo('quantidade') < 1) {
    responder(false, 'A quantidade deve ser um número inteiro maior que zero.');
}
if (!is_numeric(str_replace(',', '.', campo('custo_unitario'))) || $custo <= 0) {
    responder(false, 'O custo unitário deve ser maior que zero.');
}
if (!data_valida(campo('data_entrada'))) {
    responder(false, 'Data de entrada inválida.');
}
if (data_no_futuro(campo('data_entrada'))) {
    responder(false, 'A data de entrada não pode estar no futuro.');
}
if (!ctype_digit(campo('nota_fiscal'))) {
    responder(false, 'O número da nota fiscal deve conter apenas números.');
}

// Lógica adicional: calcula o valor total da compra.
$quantidade = (int) campo('quantidade');
$total = $quantidade * $custo;

responder(true, 'Entrada de estoque validada com sucesso.', [
    'sku' => $sku,
    'quantidade_recebida' => $quantidade,
    'valor_total_da_compra' => reais($total),
    'nota_fiscal' => campo('nota_fiscal'),
]);
