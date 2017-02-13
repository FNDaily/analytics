<?php
namespace dukt\analytics\web\assets\realtimereportwidget;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class RealtimeReportWidgetAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@dukt/analytics/resources';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
            \dukt\analytics\web\assets\analytics\AnalyticsAsset::class,
        ];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'js/RealtimeWidget.js',
        ];

        $this->css = [
            'css/RealtimeWidget.css',
        ];

        parent::init();
    }
}