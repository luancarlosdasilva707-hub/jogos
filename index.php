<?php

$conn = new mysqli("localhost", "root", "", "catalogojogos");
$conn->set_charset("utf8mb4");


if (isset($_POST["salvar"])) {
    $nome = $_POST["nome"];
    $link = $_POST["link"];
    $descricao = $_POST["descricao"];
    $codigo = $_POST["codigo"];

    if ($codigo == "") {
        $st = $conn->prepare("INSERT INTO jogos (nome, link, descricao) VALUES (?, ?, ?)");
        $st->bind_param("sss", $nome, $link, $descricao);
    } else {
        $st = $conn->prepare("UPDATE jogos SET nome=?, link=?, descricao=? WHERE codigo=?");
        $st->bind_param("sssi", $nome, $link, $descricao, $codigo);
    }
    $st->execute();
    header("Location: index.php");
    exit;
}


if (isset($_GET["excluir"])) {
    $st = $conn->prepare("DELETE FROM jogos WHERE codigo=?");
    $st->bind_param("i", $_GET["excluir"]);
    $st->execute();
    header("Location: index.php");
    exit;
}


$jogo = ["codigo" => "", "nome" => "", "link" => "", "descricao" => ""];
if (isset($_GET["editar"])) {
    $st = $conn->prepare("SELECT * FROM jogos WHERE codigo=?");
    $st->bind_param("i", $_GET["editar"]);
    $st->execute();
    $jogo = $st->get_result()->fetch_assoc();
}


$lista = $conn->query("SELECT * FROM jogos ORDER BY codigo");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Catálogo Jogos</title>
</head>
<body>
    <h2>Catálogo Jogos</h2>

    <form method="post">
        <input type="hidden" name="codigo" value="<?= $jogo['codigo'] ?>">
        Nome: <input type="text" name="nome" maxlength="250" required value="<?= htmlspecialchars($jogo['nome']) ?>"><br>
        Link: <input type="text" name="link" maxlength="250" value="<?= htmlspecialchars($jogo['link']) ?>"><br>
        Descrição:<br>
        <textarea name="descricao" rows="3" cols="40"><?= htmlspecialchars($jogo['descricao']) ?></textarea><br>
        <button type="submit" name="salvar">+ Novo / Salvar</button>
    </form>
    <hr>
    
    <?php while ($j = $lista->fetch_assoc()) { ?>
        <p>
            <?= $j["codigo"] ?> - <?= htmlspecialchars($j["nome"]) ?> -
            <a href="<?= htmlspecialchars($j["link"]) ?>"><?= htmlspecialchars($j["link"]) ?></a>
            [<a href="?editar=<?= $j["codigo"] ?>">Alterar</a>]
            [<a href="?excluir=<?= $j["codigo"] ?>" onclick="return confirm('Excluir?')">Excluir</a>]
            <br>
            <?= nl2br(htmlspecialchars($j["descricao"])) ?>
        </p>
    <?php } ?>
</body>
</html>