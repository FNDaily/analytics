<?php
/**
 * @link      https://dukt.net/craft/analytics/
 * @copyright Copyright (c) 2018, Dukt
 * @license   https://dukt.net/craft/analytics/docs/license
 */

namespace dukt\analytics\controllers;

use Craft;
use craft\helpers\FileHelper;
use craft\web\Controller;
use dukt\analytics\Plugin as Analytics;
use yii\web\Response;

/**
 * Class UtilsController
 *
 * @package dukt\analytics\controllers
 */
class UtilsController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @var bool
     */
    protected $allowAnonymous = true;

    /**
     * @var int
     */
    private $addedInApiVersion = 3;

    // Public Methods
    // =========================================================================

    /**
     * Metadata.
     *
     * @param array $variables
     *
     * @return Response
     */
    public function actionMetadata(array $variables = array())
    {
        $variables['dimensions'] = Analytics::$plugin->metadata->getDimensions();
        $variables['metrics'] = Analytics::$plugin->metadata->getMetrics();

        $variables['dimmetsFileExists'] = Analytics::$plugin->metadata->dimmetsFileExists();

        return $this->renderTemplate('analytics/utils/metadata/_index', $variables);
    }

    /**
     * Search metadata.
     */
    public function actionSearchMetadata()
    {
        $q = Craft::$app->getRequest()->getParam('q');
        $columns = Analytics::$plugin->metadata->searchColumns($q);

        // Send the source back to the template
        Craft::$app->urlManager->setRouteVariables(array(
            'q' => $q,
            'columns' => $columns,
        ));
    }

    /**
     * Loads metadata.
     *
     * @return Response
     */
    public function actionLoadMetadata()
    {
        $this->_deleteMetadata();
        $this->_importMetadata();

        Craft::$app->getSession()->setNotice(Craft::t('analytics', "Metadata loaded."));

        $referrer = Craft::$app->getRequest()->referrer;
        return $this->redirect($referrer);
    }

    // Private Methods
    // =========================================================================


    /**
     * Deletes metadata.
     */
    private function _deleteMetadata()
    {
        $path = Analytics::$plugin->metadata->getDimmetsFilePath();

        FileHelper::removeFile($path);
    }

    /**
     * Imports metadata.
     */
    private function _importMetadata()
    {
        $columns = [];

        $items = Analytics::$plugin->getApis()->getAnalytics()->getColumns();

        if($items)
        {
            foreach($items as $item)
            {
                if($item->attributes['status'] == 'DEPRECATED')
                {
                    continue;
                }

                if($item->attributes['addedInApiVersion'] > $this->addedInApiVersion)
                {
                    continue;
                }

                if(isset($item->attributes['minTemplateIndex']))
                {
                    for($i = $item->attributes['minTemplateIndex']; $i <= $item->attributes['maxTemplateIndex']; $i++)
                    {
                        $column = [];
                        $column['id'] = str_replace('XX', $i, $item->id);
                        $column['uiName'] = str_replace('XX', $i, $item->attributes['uiName']);
                        $column['description'] = str_replace('XX', $i, $item->attributes['description']);

                        $columns[$column['id']] = $this->populateColumnAttributes($column, $item);
                    }
                }
                else
                {
                    $column = [];
                    $column['id'] = $item->id;
                    $column['uiName'] = $item->attributes['uiName'];
                    $column['description'] = $item->attributes['description'];

                    $columns[$column['id']] = $this->populateColumnAttributes($column, $item);
                }
            }
        }

        $contents = json_encode($columns, JSON_PRETTY_PRINT);

        $path = Analytics::$plugin->metadata->getDimmetsFilePath();

        FileHelper::writeToFile($path, $contents);
    }

    /**
     * Populates the coloumn attribute.
     *
     * @param $column
     * @param $item
     *
     * @return mixed
     */
    private function populateColumnAttributes($column, $item)
    {
        $column['type'] = $item->attributes['type'];
        $column['dataType'] = $item->attributes['dataType'];
        $column['group'] = $item->attributes['group'];
        $column['status'] = $item->attributes['status'];

        if(isset($item->attributes['allowInSegments']))
        {
            $column['allowInSegments'] = $item->attributes['allowInSegments'];
        }

        if(isset($item->attributes['addedInApiVersion']))
        {
            $column['addedInApiVersion'] = $item->attributes['addedInApiVersion'];
        }

        return $column;
    }
}