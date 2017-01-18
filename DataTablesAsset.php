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
 * Asset for the DataTables JQuery plugin
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 */
class DataTablesAsset extends AssetBundle 
{
	public $sourcePath = '@bower/datatables'; 

	public $css = [
		// 'rezvan/global/plugins/datatables/datatables.min.css',
	];

	public $js = [
		'media/js/jquery.dataTables.js',
	];

	public $depends = [
		'yii\web\JqueryAsset',
	];
}