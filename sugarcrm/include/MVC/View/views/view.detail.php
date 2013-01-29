<?php
/*********************************************************************************
 * The contents of this file are subject to
 * *******************************************************************************/
require_once('include/DetailView/DetailView2.php');

/**
 * Default view class for handling DetailViews
 *
 * @package MVC
 * @category Views
 */
class ViewDetail extends SugarView
{
    /**
     * @see SugarView::$type
     */
    public $type = 'detail';

    /**
     * @var DetailView2 object
     */
    public $dv;

    /**
     * Constructor
     *
     * @see SugarView::SugarView()
     */
    public function __construct($bean = null, $view_object_map = array())
    {
        parent::__construct($bean, $view_object_map);
    }

    /**
     * Constructor
     *
     * @deprecated Use the PHP 5.x style __construct instead
     * @see SugarView::SugarView()
     */
    public function ViewDetail($bean = null, $view_object_map = array())
    {
        parent::SugarView($bean, $view_object_map);
    }



    /**
     * @see SugarView::preDisplay()
     */
    public function preDisplay()
    {
 	    $metadataFile = $this->getMetaDataFile();
 	    $this->dv = new DetailView2();
 	    $this->dv->ss = $this->ss;
 	    $this->dv->setup($this->module, $this->bean, $metadataFile, SugarAutoLoader::existingCustomOne('include/DetailView/DetailView.tpl'));
    }

    /**
     * @see SugarView::display()
     */
    public function display()
    {
        if(empty($this->bean->id)){
            sugar_die($GLOBALS['app_strings']['ERROR_NO_RECORD']);
        }
        $this->dv->process();
        echo $this->dv->display();
    }
}
