<?php
    function validation($request){ //$_POSTの連想配列

        $errors = [];
        // 氏名が空もしくは20文字以上だったら
        if(empty($request['your_name']) || 20 < mb_strlen($request['your_name'])){
            $errors[] = '「氏名」は必須です。20文字以内で入力してください。';
        }
        // emailの値が空もしくはemailではなかったら
        if(empty($request['email']) || !filter_var($request['email'], FILTER_VALIDATE_EMAIL)){
            $errors[] ='「メールアドレス」は必須です正しい形式で入力してください。' ;
        }
        // HPは持っていない人もいるのでURLが正しいかどうか判断する
        if(!empty($request['url'])) {
            $errors[] = '「ホームページ」は正しい形式で入力してください。';
        }

        // emptyだと通ってしまうため設定されていたらのissetを使う
        if(!isset($request['gender'])){
            $errors[] ='「性別」は必須です。';
        }
        // ageの値が空もしくは6より大きかったら
        if(empty($request['age']) || 6 < $request['age']){
            $errors[] = '「年齢」は必須です。';
        }

        // お問い合わせが空もしくは200文字より多かったら
        if(empty($request['contact']) || 200 < mb_strlen($request['contact'])){
            $errors[] = '「お問い合わせ内容」は必須です。200文字以内で入力してください。';
        }
        // 注意事項のチェックの値が空だったら
        if(empty($request['caution'])) {
            $errors[] = '「注意事項」をご確認ください。';
        }

        return $errors;

}

?>