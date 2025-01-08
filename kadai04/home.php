<?php
// 0. SESSION開始！！
session_start();

//１．関数群の読み込み
require_once('funcs.php');
loginCheck();


try {
    require_once('funcs.php'); // funcs.phpの呼び出し
    $pdo = db_conn(); // DB接続情報を格納
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // データ登録SQL作成
    $stmt = $pdo->prepare('SELECT * FROM gs_db_webcrawl_02;');
    $status = $stmt->execute();

    // データ表示
    $view = '<p>データがありません。</p>'; // デフォルトメッセージ
    if ($status === false) {
        error_log('SQLエラーが発生しました: ' . print_r($stmt->errorInfo(), true));
        exit('エラーが発生しました。管理者にお問い合わせください。');
    } else {
        $view = ''; // データが存在する場合は初期化
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $view .= '<p class="inner_rap">';
            $view .= '<a href="' . htmlspecialchars($result['link'], ENT_QUOTES, 'UTF-8') . '" id="' . htmlspecialchars($result['id'], ENT_QUOTES, 'UTF-8') . '">';
            $view .= '<img src="' . htmlspecialchars($result['image'], ENT_QUOTES, 'UTF-8') . '" alt="i">';
            $view .= '<span>' . htmlspecialchars($result['date'], ENT_QUOTES, 'UTF-8') . ' : ' . htmlspecialchars($result['tag_name'], ENT_QUOTES, 'UTF-8') . '</span>';
            $view .= '</br>';
            $view .= '<span>' . htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8') . '</span>';
            ;

            // kanri_figタグがある場合は削除を表示する。
            if ($_SESSION['kanri_flg'] === 1) {
                $view .= '</br>';
                $view .= '</a>';
                $view .= '<a href="delete.php?id=' . htmlspecialchars($result['id'], ENT_QUOTES, 'UTF-8') . '">';
                $view .= '[id:' . htmlspecialchars($result['id'], ENT_QUOTES, 'UTF-8') . ' 削除]';
                $view .= '</a>';
            }
            $view .= '</p>';
        }
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    die("エラーが発生しました。管理者にお問い合わせください。");
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>フリーアンケート表示</title>
    <link rel="stylesheet" href="css/range.css">
    <link href="css/style.css" rel="stylesheet">
    <style>
        div {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</head>

<body id="main">
    <header>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="crawlget.php">CRAWL_update</a>
                </div>
            </div>
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="rssget.php">RSS_update</a>
                </div>
            </div>

            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="logout.php">ログアウト</a>
                </div>
            </div>
        </nav>
    </header>
    <div>
        <div class="flex_outer">
            <?= $view ?>
        </div>
    </div>
</body>

</html>