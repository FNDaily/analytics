<?php

/**
 * Craft Analytics by Dukt
 *
 * @package   Craft Analytics
 * @author    Benjamin David
 * @copyright Copyright (c) 2013, Dukt
 * @license   http://dukt.net/craft/analytics/docs#license
 * @link      http://dukt.net/craft/analytics/
 */

namespace Craft;

class Analytics_ChartsController extends BaseController
{
    public function actionRealtime()
    {
        $data = array(
            'total' => '0',
            'visitorType' => array(
                'newVisitor' => 0,
                'returningVisitor' => 0
            ),
            'content' =>  array(),
            'sources' => array(),
            'countries' => array(),
            'errors' => false
        );

        try {
            $profile = craft()->analytics->getProfile();

            if(!empty($profile['id'])) {

                // visitor type

                $results = craft()->analytics->api()->data_realtime->get(
                    'ga:'.$profile['id'],
                    'ga:activeVisitors',
                    array('dimensions' => 'ga:visitorType')
                );


                if(!empty($results['totalResults'])) {
                    $data['total'] = $results['totalResults'];
                }

                if(!empty($results['rows'][0][1])) {
                    switch($results['rows'][0][0]) {
                        case "RETURNING":
                        $data['visitorType']['returningVisitor'] = $results['rows'][0][1];
                        break;

                        case "NEW":
                        $data['visitorType']['newVisitor'] = $results['rows'][0][1];
                        break;
                    }
                }

                if(!empty($results['rows'][1][1])) {
                    switch($results['rows'][1][0]) {
                        case "RETURNING":
                        $data['visitorType']['returningVisitor'] = $results['rows'][1][1];
                        break;

                        case "NEW":
                        $data['visitorType']['newVisitor'] = $results['rows'][1][1];
                        break;
                    }
                }


                // content

                $results = craft()->analytics->api()->data_realtime->get(
                    'ga:'.$profile['id'],
                    'ga:activeVisitors',
                    array('dimensions' => 'ga:pagePath')
                );

                if(!empty($results['rows'])) {
                    foreach($results['rows'] as $row) {
                        $data['content'][$row[0]] = $row[1];
                    }
                }


                // sources

                $results = craft()->analytics->api()->data_realtime->get(
                    'ga:'.$profile['id'],
                    'ga:activeVisitors',
                    array('dimensions' => 'ga:source')
                );

                if(!empty($results['rows'])) {
                    foreach($results['rows'] as $row) {
                        $data['sources'][$row[0]] = $row[1];
                    }
                }

                // countries

                $results = craft()->analytics->api()->data_realtime->get(
                    'ga:'.$profile['id'],
                    'ga:activeVisitors',
                    array('dimensions' => 'ga:country')
                );

                if(!empty($results['rows'])) {
                    foreach($results['rows'] as $row) {
                        $data['countries'][$row[0]] = $row[1];
                    }
                }
            } else {
                $data['errors'] = array(array('message' => "Please select a web profile"));
            }
        } catch(\Exception $e) {
            $data['errors'] = $e->getErrors();
        }

        $this->returnJson($data);

    }

    public function actionParse()
    {
        Craft::log(__METHOD__, LogLevel::Info, true);

        $chartQuery = $_POST['chartQuery'];

        $results = craft()->analytics->api()->data_ga->get(
            $chartQuery['param1'],
            $chartQuery['param2'],
            $chartQuery['param3'],
            $chartQuery['param4'],
            $chartQuery['param5']
        );


        // ga:keywords
        // ga:visits

        // array(2) {
        //   [0]=>
        //   string(32) ""ajax search"+"expressionengine""
        //   [1]=>
        //   string(1) "1"
        // }


        // ga:day, ga:month, ga:year
        // ga:visits
        // [0]=> array(4) {
        //     [0]=> string(2) "01" [1]=> string(2) "01" [2]=> string(4) "2012" [3]=> string(2) "13"
        // }


        $json = array(
                array(
                    'Column 1',
                    'Column 2'
                )
            );

        foreach($results['rows'] as $row) {
            $itemMetric = (int) array_pop($row);
            $itemDimension = implode('.', $row);
            // $itemDimension = md5($itemDimension);

            if($itemDimension != "(not provided)" && $itemDimension != "(not set)") {
                $item = array($itemDimension, $itemMetric);
                array_push($json, $item);
            }
        }

        $variables = array('json' => $json);

        // $templatePath = craft()->path->getPluginsPath().'analytics/templates/';

        // craft()->path->setTemplatesPath($templatePath);

        $html = craft()->templates->render('analytics/_includes/chartDraw', $variables);

        $charset = craft()->templates->getTwig()->getCharset();

        echo new \Twig_Markup($html, $charset);

        exit();
    }

    public function actionParseTable()
    {
        Craft::log(__METHOD__, LogLevel::Info, true);

        $cols = $_POST['cols'];
        $chartQuery = $_POST['chartQuery'];

        $results = craft()->analytics->api()->data_ga->get(
            $chartQuery['param1'],
            $chartQuery['param2'],
            $chartQuery['param3'],
            $chartQuery['param4'],
            $chartQuery['param5']
        );


        // ga:keywords
        // ga:visits

        // array(2) {
        //   [0]=>
        //   string(32) ""ajax search"+"expressionengine""
        //   [1]=>
        //   string(1) "1"
        // }


        // ga:day, ga:month, ga:year
        // ga:visits
        // [0]=> array(4) {
        //     [0]=> string(2) "01" [1]=> string(2) "01" [2]=> string(4) "2012" [3]=> string(2) "13"
        // }

         // var JSONObject = {
         //      cols: [{id: 'task', label: 'Task', type: 'string'},
         //          {id: 'hours', label: 'Hours per Day', type: 'number'}],
         //      rows: [{c:[{v: 'Work', p: {'style': 'border: 7px solid orange;'}}, {v: 11}]},
         //          {c:[{v: 'Eat'}, {v: 2}]},
         //          {c:[{v: 'Commute'}, {v: 2, f: '2.000'}]}]};

        $json = array(
            'cols' => $cols,
            'rows' => array()
        );

        foreach($results['rows'] as $row) {
            $itemMetric = (int) array_pop($row);
            $itemDimension = implode('.', $row);
            // $itemDimension = md5($itemDimension);

            if($itemDimension != "(not provided)" && $itemDimension != "(not set)") {
                $item = array(
                    'c' => array(
                        array('v' => '<strong>'.$itemDimension.'</strong>', 'p' => array('style' => 'border:0; border-bottom: 1px dotted #e3e5e8;')),
                        array('v' => $itemMetric, 'p' => array('style' => 'width:30%; border:0; border-bottom: 1px dotted #e3e5e8;')),
                    )
                );

                array_push($json['rows'], $item);
            }
        }

        $variables = array('json' => $json);

        // $templatePath = craft()->path->getPluginsPath().'analytics/templates/';

        // craft()->path->setTemplatesPath($templatePath);

        $html = craft()->templates->render('analytics/_includes/chartDraw', $variables);

        $charset = craft()->templates->getTwig()->getCharset();

        echo new \Twig_Markup($html, $charset);

        exit();
    }
}