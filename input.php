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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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

    <div class="container">
        <div class="row">
            <div class="col-md-6">
            <form action="input.php" method="post">
            <div class="form-group">
                <label for="your_name">氏名</label>
                <input type="text" class="form-control" name="your_name" value="<?php if(!empty($_POST['your_name'])){echo h($_POST['your_name']);} ?>" require>
            </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <!-- バリデーションのテストをするときはnameをtextにしておく -->
            <input type="email" class="form-control" id="email" name="email" value="<?php if(!empty($_POST['email'])){echo h($_POST['email']);} ?>" require>
        </div>
        <div class="form-group">
            <label for="url">ホームページ</label>
            <!-- バリデーションのテストをするときはnameをtextにしておく -->
            <input type="url" class="form-control" id="url" name="url" value="<?php if(!empty($_POST['url'])){echo h($_POST['url']);} ?>" require>
        </div>
        性別
        <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" id="gender1" name="gender" value="0"
            <?php if(isset($_POST['gender']) && $_POST['gender'] === '0')
            { echo 'checked'; }?>>
            <label class="form-check-label" for="gender1">男性</label>
        </div>
        <div class="form-check form-check-inline">
            <input type="radio" class="form-check-input" id="gender2" name="gender" value="1"
            <?php if(isset($_POST['gender']) && $_POST['gender'] === '1')
            { echo 'checked'; }?>>
            <label class="form-check-label" for="gender2">女性</label>
        </div>

        <div class="form-group">
        <label for="age">年齢</label>
        <select class="form-control" id="age" name="age">
            <option value="">選択してください</option>
            <option value="1">~19歳</option>
            <option value="2">20歳~29歳</option>
            <option value="3">30歳~39歳</option>
            <option value="4">40歳~49歳</option>
            <option value="5">50歳~59歳</option>
            <option value="6">60歳~</option>
        </select>
        </div>

        <div class="form-group">
            <label for="contact">お問い合わせ内容</label>
            <textarea class="form-control" id="contact" row="3" name="contact"><?php if(!empty($_POST['contact'])){echo h($_POST['contact']);} ?></textarea>
        </div>

        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="caution" name="caution" value="1">
            <label class="form-check-label" for="caution">注意事項にチェックする</label>
        </div>

        <input class="btn btn-info" type="submit" name="btn_confirm" value="確認する">
        <input type="hidden" name="csrf" value="<?php echo $token; ?>">
    </form>

    </div>
    </div>
    </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>