<?php
require_once('clients/summer/SideBarLayout.php');
$layout = new SideBarLayout();
$layout->push('main', array('view'=>'linkedin'));
$layout->push('main', array('view'=>'maps'));
$viewdefs['Contacts']['summer']['layout']['sidebar'] = $layout->getLayout();
