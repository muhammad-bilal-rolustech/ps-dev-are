<?php
require_once('clients/summer/SideBarLayout.php');
$layout = new SideBarLayout();
$layout->push('top', array('view'=>'subnav'));
$layout->push('main', array('view'=>'edit'));
$layout->push('side', array('layout'=>'sidebar'));
$viewdefs['summer']['layout']['edit'] = $layout->getLayout();