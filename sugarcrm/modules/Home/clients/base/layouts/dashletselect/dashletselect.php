<?php
$layout = MetaDataManager::getLayout("SideBarLayout");
$layout->push("main", array("view" => "dashletselect-headerpane"));
$layout->push("main", array("view" => "dashletselect"));
$layout->push("side", array("layout" => "sidebar"));
$viewdefs["Home"]["base"]["layout"]["dashletselect"] = $layout->getLayout();
$viewdefs["Home"]["base"]["layout"]["dashletselect"]["type"] = "dashletselect";
