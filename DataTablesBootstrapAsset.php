<?php
/**
 * @copyright dadeh kavi rezvan Co.
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-widget-datatables
 */
namespace faravaghi\datatables;
use yii\web\AssetBundle;

/**
 * Asset for the DataTables Bootstrap JQuery plugin
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 */
class DataTablesBootstrapAsset extends AssetBundle 
{
    public $sourcePath = '@bower/datatables-bootstrap3'; 

    public $css = [
        "BS3/assets/css/datatables.css",
    ];

    public $js = [
        "BS3/assets/js/datatables.js",
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'faravaghi\datatables\DataTablesAsset',
    ];
}