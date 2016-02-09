<?php
namespace Serverfireteam\Panel\libs;

use Serverfireteam\Panel\libs\CheckPermission;

class dashboard
{

    public static $dashboardItems;

    public static $urls;

    public static function getItems()
    {
        if(!self::$dashboardItems) {
            self::$dashboardItems = self::create();
        }

        return self::$dashboardItems;
    }

    public static function create()
    {
        self::$urls = \Config::get('panel.panelControllers');

        $config    = CheckPermission::getUserLinks();

        $dashboard = array();

        $appHelper = new AppHelper();

        // Make Dashboard Items
    	if (!empty($config)) {
    	        foreach ($config as $key => $value) {
    			$modelName = $value['url'];
    			if (in_array($modelName, self::$urls)) {
            			$model = "Serverfireteam\Panel\\".$modelName;
    	       		} else {
    				$appHelper = new AppHelper();
                   			$model = $appHelper->getNameSpace() . $modelName;
    	       		}

    		        $dashboard[] = array(
                   			'title'	      => $value['display'],
                   			'count'	      => $model::all()->count(),
    	               		'showListUrl' => 'panel/' . $modelName . '/all',
            	       		'addUrl'      => 'panel/' . $modelName . '/edit',
           			);
    	        }
    	}

	   return $dashboard;
    }
}
