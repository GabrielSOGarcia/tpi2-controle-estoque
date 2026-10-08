<?php
// Funções compartilhadas por todos os endpoints.
// O servidor nunca devolve HTML: apenas JSON, que o js/app.js exibe na tela.
header('Content-Type: application/json; charset=utf-8');

function responder($ok, $mensagem, $dados = [])
{
    echo json_encode(['ok' => $ok, 'mensagem' => $mensagem, 'dados' => $dados], JSON_UNESCAPED_UNICODE);
    exit;
}

function exigir_post()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        responder(false, 'Método não permitido. Use POST.');
    }
}

function campo($nome)
{
    return trim($_POST[$nome] ?? '');
}

function exigir_campos($lista)
{
    $faltando = [];
    foreach ($lista as $nome) {
        if (campo($nome) === '') {
            $faltando[] = $nome;
        }
    }
    if ($faltando) {
        responder(false, 'Campos obrigatórios não preenchidos: ' . implode(', ', $faltando) . '.');
    }
}

function numero($nome)
{
    return (float) str_replace(',', '.', campo($nome));
}

function data_valida($texto)
{
    $d = DateTime::createFromFormat('Y-m-d', $texto);
    return $d && $d->format('Y-m-d') === $texto;
}

function data_no_futuro($texto)
{
    return $texto > date('Y-m-d');
}

function reais($valor)
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Tamanho de texto em caracteres (funciona mesmo sem a extensão mbstring).
function tamanho($texto)
{
    return function_exists('mb_strlen') ? mb_strlen($texto, 'UTF-8') : preg_match_all('/./us', $texto);
}

// Remove acentos de um texto (funciona mesmo sem a extensão iconv).
function sem_acentos($texto)
{
    $de = ['á','à','â','ã','ä','é','è','ê','ë','í','ì','î','ï','ó','ò','ô','õ','ö','ú','ù','û','ü','ç','Á','À','Â','Ã','Ä','É','È','Ê','Ë','Í','Ì','Î','Ï','Ó','Ò','Ô','Õ','Ö','Ú','Ù','Û','Ü','Ç'];
    $para = ['a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','o','u','u','u','u','c','A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','C'];
    return str_replace($de, $para, $texto);
}

// Primeira letra de cada palavra em maiúscula.
function titulo($texto)
{
    if (function_exists('mb_convert_case')) {
        return mb_convert_case($texto, MB_CASE_TITLE, 'UTF-8');
    }
    return ucwords(strtolower($texto));
}
