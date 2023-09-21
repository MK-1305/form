<?php
    // CSRF（偽物のinput.php->悪意あるページへ）対策：本物かどうかの合言葉を決める
    session_start();

    require('validation.php');
    // クリックジャっキング（cssで透明なボタンを置いて攻撃）対策
    header('X-FRAME-OPTIONS:DENY');

    // スーパーグローバル変数 phpは９種類
    // 連想配列(キーはname)
    if(!empty($_POST)) {
        echo '<pre>';
        var_dump($_POST);
        echo '</pre>';
    }

    // XSS（フォームにコードを書く）対策
    function h($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    // 入力、確認、完了

    // 入力画面
    $pageFlag = 0;
    $errors = validation($_POST);

    // btn_confirmに値が入っていたら
    if(!empty($_POST['btn_confirm']) && empty($errors)){
        $pageFlag = 1;
    }
    // btn_submitに値が入っていたら
    if(!empty($_POST['btn_submit'])){
        $pageFlag = 2;
    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php if($pageFlag === 1) : ?>
    <!-- 合言葉が合っているかの確認 -->
    <?php if($_POST['csrf'] === $_SESSION['csrfToken']) :?>
        <form action="input.php" method="post">
        氏名
        <?php echo h($_POST['your_name']); ?>
        <br>
        メールアドレス
        <?php echo h($_POST['email']); ?>
        <br>
        ホームページ
        <?php echo h($_POST['url']); ?>
        <br>
        性別
        <?php
            if($_POST['gender'] === '0'){ echo '男性'; }
            if($_POST['gender'] === '1'){ echo '女性'; }
        ?>
        <br>
        年齢
        <?php
            if($_POST['age'] === '1'){ echo '~19歳' ;}
            if($_POST['age'] === '1'){ echo '20~29歳' ;}
            if($_POST['age'] === '1'){ echo '30~39歳' ;}
            if($_POST['age'] === '1'){ echo '40~49歳' ;}
            if($_POST['age'] === '1'){ echo '50~59歳' ;}
            if($_POST['age'] === '1'){ echo '60歳~' ;}
        ?>
        <br>
        お問い合わせ内容
        <?php echo h($_POST['contact']); ?>
        <br>
        <input type="submit" name="back" value="戻る">
        <input type="submit" name="btn_submit" value="送信する">
        <input type="hidden" name="your_name" value="<?php echo h($_POST['your_name']); ?>">
        <input type="hidden" name="email" value="<?php echo h($_POST['email']); ?>">
        <input type="hidden" name="url" value="<?php echo h($_POST['url']); ?>">
        <input type="hidden" name="gender" value="<?php echo h($_POST['gender']); ?>">
        <input type="hidden" name="age" value="<?php echo h($_POST['age']); ?>">
        <input type="hidden" name="contact" value="<?php echo h($_POST['contact']); ?>">

        <input type="hidden" name="csrf" value="<?php echo h($_POST['csrf']); ?>">
    </form>
    <?php endif; ?>

    <?php endif; ?>

    <?php if($pageFlag === 2) : ?>
    <!-- 合言葉が合っているかの確認 -->
    <?php if($_POST['csrf'] === $_SESSION['csrfToken']) :?>
    送信が完了しました
    
    <!-- 完了画面で合言葉設定も解除される -->
    <?php unset($_SESSION['csrfToken']); ?>
    <?php endif; ?>
    <?php endif; ?>

    <?php if($pageFlag === 0) : ?>
    
    <!-- CSRF用の合言葉を作る -->
    <?php
    // 毎回作られるのを防ぐため、$_SESSION['csrfToken']が作られていなかったら
    if(!isset($_SESSION['csrfToken'])){
        // bin2hecで2進数（バイナリ）を16進数に変える
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrfToken'] = $csrfToken;
    }
    // 長いので短くする
    $token = $_SESSION['csrfToken'];
    ?>

    <?php if(!empty($errors) && !empty($_POST['btn_confirm'])) : ?>
    <?php echo '<ul>' ;?>
    <?php
        foreach($errors as $error){
            echo '<li>' . $error . '</li>';
        }
    ?>
    <?php echo '</ul>' ; ?>
    <?php endif ?>

    <form action="input.php" method="post">
        氏名
        <input type="text" name="your_name" value="<?php if(!empty($_POST['your_name'])){echo h($_POST['your_name']);} ?>">
        <br>
        メールアドレス
        <!-- バリデーションのテストをするときはnameをtextにしておく -->
        <input type="email" name="email" value="<?php if(!empty($_POST['email'])){echo h($_POST['email']);} ?>">
        <br>
        ホームページ
        <!-- バリデーションのテストをするときはnameをtextにしておく -->
        <input type="url" name="url" value="<?php if(!empty($_POST['url'])){echo h($_POST['url']);} ?>">
        <br>
        性別
        <input type="radio" name="gender" value="0"
        <?php if(isset($_POST['gender']) && $_POST['gender'] === '0')
        { echo 'checked'; }?>>男性

        <input type="radio" name="gender" value="1"
        <?php if(isset($_POST['gender']) && $_POST['gender'] === '1')
        { echo 'checked'; }?>>女性
        <br>
        年齢
        <select name="age">
            <option value="">選択してください</option>
            <option value="1">~19歳</option>
            <option value="2">20歳~29歳</option>
            <option value="3">30歳~39歳</option>
            <option value="4">40歳~49歳</option>
            <option value="5">50歳~59歳</option>
            <option value="6">60歳~</option>
        </select>
        <br>
        お問い合わせ内容
        <textarea name="contact"><?php if(!empty($_POST['contact'])){echo h($_POST['contact']);} ?></textarea>
        <br>
        注意事項のチェック
        <input type="checkbox" name="caution" value="1">注意事項にチェックする
        <br>
        <input type="submit" name="btn_confirm" value="確認する">
        <input type="hidden" name="csrf" value="<?php echo $token; ?>">
    </form>
    <?php endif; ?>
</body>
</html>