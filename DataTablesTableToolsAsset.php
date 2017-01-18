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
 * Asset for the DataTables TableTools JQuery plugin
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 */
class DataTablesTableToolsAsset extends AssetBundle 
{
    public $sourcePath = '@bower/datatables-tabletools'; 

    public $css = [
        // "css/dataTables.tableTools.css",
    ];

    public $js = [
        "js/dataTables.tableTools.js",
    ];

    public $depends = [
        'yii\web\JqueryAsset',
        'faravaghi\datatables\DataTablesAsset',
    ];
}