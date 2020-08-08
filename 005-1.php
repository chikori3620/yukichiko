<!DOCTYPE html>
<html lang ="ja">
<head>
    <meta charset ="UTF-8">
    <title>mission5-1</title>
<body>
<?php
    $name=$_POST["name"];
    $comment=$_POST["comment"];
    $inputPass=$_POST["inputPass"];
    $editNum=$_POST["editNum"];
    $delete=$_POST["delete"];
    $deletePass=$_POST["deletePass"];
    $edit=$_POST["edit"];
    $editPass=$_POST["editPass"];
    $date=date("Y/m/d H:i:s");

//DB接続(4-1)
    $dsn='データソース名';
    $user='ユーザー名';
    $password='パスワード';
    $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
    //DB接続確認
    if($pdo){
        echo "データベース接続OK";
    }else{
        echo "データベース未接続";
    } 

//テーブル作成(4-2)
    $sql="CREATE TABLE IF NOT EXISTS Mission5_1" //データベース名に-(マイナス)は使えない
    ."("
    ."id INT AUTO_INCREMENT PRIMARY KEY,"
    ."name char(32),"
    ."comment TEXT"
    .");";
    $stmt=$pdo->query($sql);
    //日付とパスワードのカラムを付け忘れたので、ALTERでカラム追加
    $sql="ALTER TABLE Mission5_1 ADD date DATETIME";
    $stmt=$pdo->query($sql);
    $sql="ALTER TABLE Mission5_1 ADD password TEXT";
    $stmt=$pdo->query($sql);

//【新規投稿】名前、コメントが送信された場合のみ、テキストファイルに追記する
    if(!empty($name) && !empty($comment) && empty($editNum) && !empty($inputPass)){
        $sql=$pdo->prepare("INSERT INTO Mission5_1(name,comment,date,password)VALUES(:name,:comment,:date,:password) ");
        $sql->bindParam(':name',$name,PDO::PARAM_STR);
        $sql->bindParam(':comment',$comment,PDO::PARAM_STR);
        $sql->bindParam(':date',$date,PDO::PARAM_STR);
        $sql->bindParam(':password',$inputPass,PDO::PARAM_STR);
        $sql->execute();
        //SQL文の間違い確認
        if($sql){
            echo "登録成功"."<br>";
        }else{
            echo "登録失敗"."<br>";
        }
    }

//【削除】①削除番号とパスワードが指定のidとpasswordと一致していたら削除②それ以外なら、そのまま表示
    if(!empty($delete) && !empty($deletePass)){
        //消したい投稿のid,passwordを取得する
        $id=$delete; //この値のデータだけを抽出したい
        $sql="SELECT * FROM Mission5_1 WHERE id=:id";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(":id",$id,PDO::PARAM_INT);
        $stmt->execute();
        $results=$stmt->fetchAll();
        foreach($results as $row){
            //削除実行する条件
            if($delete==$row["id"] && $deletePass==$row["password"]){
                $id=$delete; //このidのデータだけ抽出したい
                $sql="DELETE FROM Mission5_1 WHERE id=:id";
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(":id",$id,PDO::PARAM_INT);
                $stmt->execute();
             }
        //SQL文の間違い確認
        if($sql){
            echo "登録成功"."<br>";
        }else{
            echo "登録失敗"."<br>";
        }
        }
    }

//【1編集選択機能】①編集対象番号とパスワードが指定のidと一致していたら、入力フォームに内容コピー②それ以外なら、変化なし
    if(!empty($edit) && !empty($editPass)){
        //編集したい投稿のid,passwordを取得する
        $id=$edit; //この値のデータだけを抽出したい
        $sql="SELECT * FROM Mission5_1 WHERE id=:id";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(":id",$id,PDO::PARAM_INT);
        $stmt->execute();
        $results=$stmt->fetchAll();
        foreach($results as $row){
            //投稿フォームに内容コピー
            if($edit==$row["id"] && $editPass==$row["password"]){
                $id=$edit; //このデータだけ抽出したい
                $nameCopy=$row["name"];
                $commentCopy=$row["comment"];
                $editNumCopy=$row["id"];
            }
        }
        //SQL文の間違い確認
        if($sql){
            echo "登録成功"."<br>";
        }else{
            echo "登録失敗"."<br>";
        }
    }

//【2編集実行機能】idと編集対象番号が同じ場合、投稿内容を送信内容に差し替える（更新）
    if(!empty($name) && !empty($comment) && !empty($inputPass) && !empty($editNum)){
        //更新したい投稿のid,passwordを取得する
        $id=$editNum; //この値のデータだけを抽出
        $sql="UPDATE Mission5_1 SET name=:name, comment=:comment, date=:date, password=:password WHERE id=:id";
                                                                              //↑ここでパスワードも変更できるようになる
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(":id",$id,PDO::PARAM_INT);
        $stmt->bindParam(":name",$name,PDO::PARAM_STR);
        $stmt->bindParam(":comment",$comment,PDO::PARAM_STR);
        $stmt->bindParam(":date",$date,PDO::PARAM_STR);
        $stmt->bindParam(":password",$inputPass,PDO::PARAM_STR);
        $stmt->execute();
         //SQL文の間違い確認
        if($sql){
            echo "登録成功"."<br>";
        }else{
            echo "登録失敗"."<br>";
        }
    }
?>

<!--入力フォーム作成-->
    <form action="" method="post">
        名前：<input type="text" name="name" placeholder="山田太郎" value="<?php echo $nameCopy?>"><br>
        コメント：<input type="text" name="comment" placeholder="コメント" value="<?php echo $commentCopy?>"><br>
        パスワード：<input type="text" name="inputPass" placeholder="パスワード">
        <input type="hidden" name="editNum" placeholder="編集対象投稿番号" value="<?php echo $editNumCopy?>">
        <input type="submit" name="submit" value="送信"><br><br>
        削除：<input type="number" name="delete" placeholder="削除対象番号"><br>
        パスワード：<input type="text" name="deletePass" placeholder="パスワード">
        <input type="submit" name="submit" value="削除"><br><br>
        編集対象番号：<input type="number" name="edit" placeholder="編集対象番号"><br>
        パスワード：<input type="text" name="editPass" placeholder="パスワード">
        <input type="submit" name="submit" value="編集"><br><br>
    </form>
    
<?php
//SELECT文で、ブラウザに表示
        $sql="SELECT * FROM Mission5_1";
        $stmt=$pdo->query($sql);
        $results=$stmt->fetchAll();
        foreach($results as $row){
            echo $row["id"].",";
            echo $row["name"].",";
            echo $row["comment"].",";
            echo $row["date"].",";
            echo $row["password"]."<br>";
            echo "<hr>";
        }
?>
</body>
</head>
</html>