<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?= $title ?? '' ?></title>
	<style>
    <?php
foreach ($css ?? [] as $css_file) {
  echo file_get_contents("css/$css_file.css")."\n";
}
?>
 ?>
	</style>
</head>
<body>
<?= $body ?? 'no body defined' ?>
</body>
</html>