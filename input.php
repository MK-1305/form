<?php
    // スーパーグローバル変数 phpは９種類
    // 連想配列(キーはname)
    if(!empty($_POST)) {
        echo '<pre>';
        var_dump($_POST);
        echo '</pre>';
    }

    // 入力、確認、完了

    $pageFlag =

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php if($pageFlag === 0) : ?>
    入力画面
    <?php endif; ?>

    <?php if($pageFlag === 1) : ?>
    確認画面
    <?php endif; ?>

    <?php if($pageFlag === 2) : ?>
    完了画面
    <?php endif; ?>

    <form action="input.php" method="post">
        氏名
        <input type="text" name="your_name">
        <br>
        <input type="checkbox" name="sports[]" value="野球">野球
        <input type="checkbox" name="sports[]" value="サッカー">サッカー
        <input type="checkbox" name="sports[]" value="バスケ">バスケ
        <input type="submit" value="送信">
    </form>
</body>
</html>