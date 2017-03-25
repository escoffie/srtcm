<div id="logo"></div>

<div id="caption">CMS <small><?php echo $version ?></small></div>

<ul id="leftmenu">

<?php

	foreach($pagedata as $pk=>$pd){ 
		if($_SESSION['level']<=$pd['level']){
?>

    <li class="<?php echo $page == $pk ? 'active' : '' ?>">


        <a href="index.php?page=<?php echo $pk ?>"><?php echo $pd['title_1'] ?></a>

    </li>

<?php	   
		}
	}

?>
	<li><a href="logout.php">Cerrar sesión</a></li>

</ul>