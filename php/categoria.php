<?php
require __DIR__ . '/util.php';
exigir_post();
exigir_campos(['nome', 'descricao', 'setor', 'prioridade', 'ativa']);

$nome = campo('nome');
$descricao = campo('descricao');
$setores = ['eletronicos', 'games', 'eletrodomesticos', 'acessorios'];
$prioridades = ['alta', 'media', 'baixa'];

if (tamanho($nome) < 3) {
    responder(false, 'O nome da categoria deve ter pelo menos 3 caracteres.');
}
if (tamanho($descricao) < 10) {
    responder(false, 'A descrição deve ter pelo menos 10 caracteres.');
}
if (!in_array(campo('setor'), $setores, true)) {
    responder(false, 'Setor inválido.');
}
if (!in_array(campo('prioridade'), $prioridades, true)) {
    responder(false, 'Prioridade inválida.');
}
if (!in_array(campo('ativa'), ['sim', 'nao'], true)) {
    responder(false, 'Informe se a categoria está ativa.');
}

// Lógica adicional: gera um código curto para a categoria (3 primeiras letras do nome).
$letras = preg_replace('/[^A-Za-z]/', '', sem_acentos($nome));
if (strlen($letras) < 3) {
    responder(false, 'O nome da categoria precisa ter pelo menos 3 letras.');
}
$codigo = strtoupper(substr($letras, 0, 3));

responder(true, 'Categoria validada com sucesso.', [
    'categoria' => titulo($nome),
    'codigo_gerado' => $codigo,
    'situacao' => campo('ativa') === 'sim' ? 'Disponível para novos produtos' : 'Inativa',
]);
