<!DOCTYPE html>
<html lang="en" class="app" itemscope itemtype="http://schema.org/Article">
<head>  
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>
<body>
    <span style="font-family:'comic sans ms','sans-serif'">
	Hello, <?php echo $username ?>
	<br>
	<br>
        <a href="http://localhost:8000/export_latlng/<?php echo $order_id ?>">Klik disini untuk download KML</a>
	<br>
	<br>
	Terima Kasih
    </span>
</body>
</html>