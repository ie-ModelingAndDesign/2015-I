<?php
$timestamp = strtotime("+8 hour");
$topic_num = 10;

function get_form($str, $multi = false) {
  $str = htmlspecialchars($str);
  $str = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $str);
  if ($multi) {
    $str = str_replace("\r\n", "<br>", $str);
    $str = str_replace("\r", "<br>", $str);
    $str = str_replace("\n", "<br>", $str);
  }
  return $str;
}

$page = $_GET['page'];
if (!$page) $page = '0';
if ($_POST['write'] || $_POST['delete']) $log_change = true;
else $log_change = false;
$lines = array();
$fp = fopen('keijiban.txt', 'r+');
if ($log_change) flock($fp, LOCK_EX);
while (!feof($fp)) $lines[] = fgets($fp, 10000);
array_pop($lines);
if (!$log_change) fclose($fp);
if ($_POST['write']) {
  $name = get_form($_POST['name']);
  $contents = get_form($_POST['contents'], true);
  if (!$name) echo "<font color='red'> 料理名を入力してください </font>";
  if ($name and !$contents) echo "<font color='red'> 本文を入力してください </font>";
    if (!(!$name or !$contents)) {
      $maxno = 0;
      foreach($lines as $line) {
        $items = explode("\t", $line);
        if ($maxno < $items[0]) $maxno = $items[0];
      }
      $no = $maxno + 1;
      $delkey = get_form($_POST['delkey']);
      $time = date("Y/m/d H:i:s",$timestamp);
      $data = "$no\t$name\t$contents\t$delkey\t$time\n";
      array_unshift($lines, $data);
    }
}
if ($_POST['delete']) {
  $dno = $dnum = 0;
  for ($i = 0; $i < count($lines); $i++) {
    $items = explode("\t", $lines[$i]);
    if ($items[0] == $_POST['delno']) {
      if ($items[3] == $_POST['delkey2']) {
        $dno = $i;
        $dnum++;
      } else {
        echo "<font color='red'> 削除キーが間違っています </font>";
        goto jump;
      }
    }
  }
  if ($dnum) array_splice($lines, $dno, $dnum);
  else echo "<font color='red'>  指定した番号のレビューはありません</font>";
}
jump:
if ($log_change) {
  $past = false;
  $topic_no = 0;
  rewind($fp);
  ftruncate($fp, 0);
  for ($i = 0; $i < count($lines); $i++) {
    $items = explode("\t", $lines[$i]);
    $topic_no++;
    fputs($fp, $lines[$i]);
  }
  fclose($fp);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>料理のレビュー</title>
</head>
<body bgcolor="#FFFAF0">
</form>
<form action = "keijiban.php" method = "get">
<input type = "text" name ="search" placeholder="料理名を入力"><input type = "submit" value ="検索"><hr>
</form>
<form method="post" action="keijiban.php">
料理名：<input type="text" name="name" value="<?php print $search ?>">　削除キー：<input type="password" name="delkey" value="<?php print $delkey ?>" size="8"><br>
<textarea name="contents" cols="60" rows="5"></textarea><br>
<?php
  print "<input type='submit' name='write' value='レビューを投稿'><br>\n";
  print "<hr>番号：No.<input type='text' name='delno' size='5'>\n";
  print "　削除キー: <input type='password' name='delkey2' size='8'>\n";
  print "　<input type='submit' name='delete' value='レビュー削除'>\n";
?>
</form>
<?php
$topic_no = 0;
$next_page = 0;
foreach ($lines as $line) {
  $items = explode("\t", rtrim($line));
  $topic_no++;
  
  if ($_GET['search']) {
    if (!(stristr($items[1], $_GET['search']))) continue;
  } else if ($topic_no <= $page * $topic_num) {
    continue;
  } else if ($topic_no > ($page + 1) * $topic_num) {
    $next_page = $page + 1;
    break;
  }
  print "<hr><p>No.{$items[0]}　料理名：<b>{$items[1]}</b>　投稿日時：{$items[4]}";
  print "<br><br>{$items[2]}</p>\n";
}
print "<hr>\n";
if ($page > 0) {
  $prev_page = $page - 1;
  print "<a href='keijiban.php?page=0'>TOP</a>　";
  print "<a href='keijiban.php?page=$prev_page'>前ページ</a>　";
}
if ($next_page) {
  print "<a href='keijiban.php?page=$next_page'>次ページ</a>";
}
print "\n";
?>
</body>
</html>