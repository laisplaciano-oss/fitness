
<?php
$erro = "";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit("Acesse o sistema pelo formulário de matrícula.");
}

$nome = trim($_POST["nome"] ?? "");
$idade = filter_var($_POST["idade"] ?? null, FILTER_VALIDATE_INT);
$telefone = trim($_POST["telefone"] ?? "");
$email = trim($_POST["email"] ?? "");
$data = $_POST["data"] ?? "";
$plano = $_POST["plano"] ?? "";
$pagamento = $_POST["pagamento"] ?? "";

// Validar os dados recebidos
if (
    $nome === "" ||
    $idade === false ||
    $idade === null ||
    $telefone === "" ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $data === "" ||
    !checkdate(
        (int) substr($data, 5, 2),
        (int) substr($data, 8, 2),
        (int) substr($data, 0, 4)
    ) ||
    $data > date("Y-m-d") ||
    !in_array($plano, ["basico", "plus", "premium"], true) ||
    !in_array($pagamento, ["pix", "cartao"], true)
) {
    $erro = "Preencha todos os campos corretamente.";
} elseif ($idade < 18) {
    $erro = "A matrícula só é permitida para maiores de idade a partir de 18 anos.";
}

if ($erro !== "") {
    echo "<h2>Não foi possível realizar a matrícula</h2>";
    echo "<p>" . htmlspecialchars($erro, ENT_QUOTES, "UTF-8") . "</p>";
    echo '<a href="cadastro.html">Voltar ao formulário</a>';
    exit;
}

// Preços dos planos
$planos = [
    "basico" => ["nome" => "Básico", "preco" => 89.90],
    "plus" => ["nome" => "Plus", "preco" => 129.90],
    "premium" => ["nome" => "Premium", "preco" => 179.90]
];

$nomePlano = $planos[$plano]["nome"];
$valorOriginal = $planos[$plano]["preco"];

// Calcular o desconto sem acumular percentuais
if ($idade >= 60) {
    $percentual = 15;
} elseif ($pagamento === "pix") {
    $percentual = 10;
} else {
    $percentual = 0;
}

$valorDesconto = round($valorOriginal * $percentual / 100, 2);
$total = round($valorOriginal - $valorDesconto, 2);

// Formatar valores em reais
function reais($valor) {
    return "R$ " . number_format($valor, 2, ",", ".");
}

// Escapar dados exibidos na página
function seguro($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante | Fitness Manager</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="topo">
    <h1>FITNESS MANAGER</h1>
    <p>Comprovante de matrícula</p>
</header>

<main class="container">
    <section class="card">
        <h2>Matrícula realizada com sucesso!</h2>
        <p>Confira os dados informados:</p>

        <p><strong>Nome:</strong> <?= seguro($nome) ?></p>
        <p><strong>Idade:</strong> <?= seguro($idade) ?> anos</p>
        <p><strong>Telefone:</strong> <?= seguro($telefone) ?></p>
        <p><strong>E-mail:</strong> <?= seguro($email) ?></p>

        <p><strong>Data da matrícula:</strong>
            <?= date("d/m/Y", strtotime($data)) ?></p>

        <p><strong>Plano:</strong> <?= seguro($nomePlano) ?></p>

        <p><strong>Pagamento:</strong>
            <?= $pagamento === "pix" ? "PIX" : "Cartão" ?></p>

        <hr>

        <p><strong>Valor original:</strong>
            <?= reais($valorOriginal) ?></p>

        <p><strong>Desconto aplicado:</strong>
            <?= $percentual ?>% (<?= reais($valorDesconto) ?>)</p>

        <h2>Total a pagar: <?= reais($total) ?></h2>

        <a href="cadastro.html" class="botao">Nova matrícula</a>
        <button onclick="window.print()" class="botao secundario">
            Imprimir comprovante
        </button>
    </section>
</main>

<footer>
    <p>Fitness Manager © 2026</p>
</footer>

</body>
</html>
