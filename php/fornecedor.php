<?php
require __DIR__ . '/util.php';
exigir_post();
exigir_campos(['razao_social', 'cnpj', 'email', 'telefone', 'cidade', 'prazo_entrega']);

function cnpj_valido($cnpj)
{
    if (strlen($cnpj) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpj)) {
        return false;
    }
    foreach ([12, 13] as $tamanho) {
        $pesos = $tamanho === 12 ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2] : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $soma = 0;
        for ($i = 0; $i < $tamanho; $i++) {
            $soma += (int) $cnpj[$i] * $pesos[$i];
        }
        $resto = $soma % 11;
        $digito = $resto < 2 ? 0 : 11 - $resto;
        if ((int) $cnpj[$tamanho] !== $digito) {
            return false;
        }
    }
    return true;
}

$cnpj = preg_replace('/\D/', '', campo('cnpj'));
$telefone = preg_replace('/\D/', '', campo('telefone'));

if (tamanho(campo('razao_social')) < 3) {
    responder(false, 'A razão social deve ter pelo menos 3 caracteres.');
}
if (!cnpj_valido($cnpj)) {
    responder(false, 'CNPJ inválido.');
}
if (!filter_var(campo('email'), FILTER_VALIDATE_EMAIL)) {
    responder(false, 'E-mail inválido.');
}
if (strlen($telefone) < 10 || strlen($telefone) > 11) {
    responder(false, 'Telefone inválido: informe o DDD e o número (10 ou 11 dígitos).');
}
if (!ctype_digit(campo('prazo_entrega')) || (int) campo('prazo_entrega') < 1 || (int) campo('prazo_entrega') > 90) {
    responder(false, 'O prazo de entrega deve ser de 1 a 90 dias.');
}

// Lógica adicional: classifica o fornecedor pelo prazo de entrega.
$prazo = (int) campo('prazo_entrega');
if ($prazo <= 7) {
    $classe = 'Entrega rápida';
} elseif ($prazo <= 30) {
    $classe = 'Entrega normal';
} else {
    $classe = 'Entrega lenta (planeje a reposição com antecedência)';
}

responder(true, 'Fornecedor validado com sucesso.', [
    'fornecedor' => campo('razao_social'),
    'cnpj_formatado' => preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj),
    'classificacao' => $classe,
]);
