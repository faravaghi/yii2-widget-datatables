<?php
/**
 * @copyright dadeh kavi rezvan Co.
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-widget-datatables
 */
namespace faravaghi\datatables;

use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\db\ActiveQuery;
use yii\base\InvalidConfigException;

/**
 * Datatables Yii2 widget
 * @author Mohammd Ebrahim Amini <info@faravaghi.ir>
 */
class DataTables extends \yii\grid\GridView
{
	/**
	* @var array the HTML attributes for the container tag of the datatables view.
	* The "tag" element specifies the tag name of the container element and defaults to "div".
	* @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	*/
	public $options = [];
	
	/**
	* @var array the HTML attributes for the datatables table element.
	* @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	*/
	public $tableOptions = [
		'class' => 'table table-striped table-bordered dataTable no-footer dtr-inline',
		'cellspacing' => '0',
		'width' => '100%'
	];
	
	/**
	* @var array the HTML attributes for the datatables table element.
	* @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
	*/
	public $clientOptions = [];

	/**
	 * @var array of Columns that you want sort with this field
	 */
	public $sortableColumn = [];

	/**
	 * @var array of Columns that search on its
	 */
	public $searchableColumn = [];
	
	/**
	 * Initializes the datatables widget disabling some GridView options like 
	 * search, sort and pagination and using DataTables JS functionalities 
	 * instead.
	 */
	public function init()
	{
		parent::init();
		$this->registerTranslations();

		/**
		 * init clientOptions:
		 */
		$clientOptions = [
			/*'processing' => true,
			'ordering' => false,*/
			'order' => [
				[ 1, 'asc' ]
			],
			'serverSide'=> true,
			'lengthMenu'=> [
				[10, 20, 50, -1],
				[10, 20, 50, self::t('edt','All')]
			],
			'columnDefs'=> [
				[
					'orderable' => false,
					'targets' => [0, count($this->columns) - 1]
				]
			],
			'sPaginationType' => 'bootstrap',
			'responsive'=>true,
			'dom'=> "<'row'<'col-md-12'<'dt-buttons hidden-xs'T>>><'row'<'col-md-6 col-sm-6 col-xs-6'l><'col-md-6 col-sm-6 col-xs-6'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-5 col-xs-12'i><'col-md-7 col-sm-7 col-xs-12'p>>",
			'tableTools'=>[
				'aButtons'=> [  
					[
						'sExtends'=> 'copy',
						'sButtonText'=> self::t('edt','Copy to Clipboard'),
						'sButtonClass' => 'dt-button buttons-copy buttons-flash btn red btn-outline',
						'sInfo' => self::t('edt', '<h6>Table copied</h6>')
					],
					/*[
						'sExtends'=> 'csv',
						'sButtonText'=> Self::t('edt','Save to CSV')
					],*/
					[
						'sExtends'=> 'xls',
						'sButtonText'=> self::t('edt','Save as Excel'),
						'sButtonClass' => 'dt-button buttons-excel buttons-flash btn yellow btn-outline',
						'oSelectorOpts'=> ['page'=> 'current']
					],
					/*[
						'sExtends'=> 'pdf',
						'sButtonText'=> self::t('edt','Save to PDF'),
						'sButtonClass' => 'dt-button buttons-pdf buttons-html5 btn green btn-outline',
					],*/
					[
						'sExtends'=> 'print',
						'sButtonText'=> self::t('edt','Print'),
						'sButtonClass' => 'dt-button buttons-print btn dark btn-outline',
						'sInfo'=> self::t('edt',"<h6>Print view</h6><p>Please use your browser's print function to print this table. Press escape when finished."),
					],
				]
			],
			'language'=>[
				'processing'	=> self::t('edt', 'Processing...'),
				'search'		=> self::t('edt', 'Search:'),
				'lengthMenu'	=> self::t('edt','Show _MENU_ entries'),
				'info'		  => self::t('edt','Showing _START_ to _END_ of _TOTAL_ entries'),
				'infoEmpty'	 => self::t('edt','Showing 0 to 0 of 0 entries'),
				'infoFiltered'  => self::t('edt','(filtered from _MAX_ total entries)'),
				'infoPostFix'   => '',
				'loadingRecords'=> self::t('edt','Loading...'),
				'zeroRecords'   => self::t('edt','No matching records found'),
				'emptyTable'	=> self::t('edt','No data available in table'),
				'paginate' => [
					'first'	 => self::t('edt','First'),
					'previous'  => self::t('edt','Previous'),
					'next'	  => self::t('edt','Next'),
					'last'	  => self::t('edt','Last'),
				],
				'aria' => [
					'sortAscending' => self::t('edt',': activate to sort column ascending'),
					'sortDescending'=> self::t('edt',': activate to sort column descending'),
				]
			]
		];

		/**
		 * Merge Defualt Client Options and User definition:
		 */
		$this->clientOptions = array_merge($clientOptions, $this->getClientOptions());
		
		//disable filter model by grid view
		// $this->filterModel = null;

		//layout showing only items
		$this->layout = "{items}";

		//the table id must be set
		if (!isset($this->tableOptions['id'])) {
			$this->tableOptions['id'] = $this->getId();
		}
	}

	/**
	 * Runs the widget.
	 */
	public function run()
	{
		$view = $this->getView();
		$id = $this->tableOptions['id'];
		
		/**
		 * Load ClientOptions:
		 */
		$clientOptions = $this->getClientOptions();

		//Bootstrap3 Asset by default
		DataTablesBootstrapAsset::register($view);

		//TableTools Asset if needed
		if (isset($clientOptions["tableTools"]) || (isset($clientOptions["dom"]) && strpos($clientOptions["dom"], 'T')>=0)){
			$tableTools = DataTablesTableToolsAsset::register($view);
			//SWF copy and download path overwrite
			$clientOptions["tableTools"]["sSwfPath"] = $tableTools->baseUrl."/swf/copy_csv_xls_pdf.swf";
		}

		$options = Json::encode($clientOptions);
		$view->registerJs("var table = $('#$id').DataTable($options);" , \yii\web\View::POS_END);
		$view->registerJs('$(\'body\').append(\'<div tabindex="-1" role="dialog" class="fade modal" id="view-modal"><div class="modal-dialog modal-lg"><div class="modal-content"></div></div></div>\');');

		//base list view run
		if ($this->showOnEmpty || $this->dataProvider->getCount() > 0) {
			$content = preg_replace_callback("/{\\w+}/", function ($matches) {
				// $content = $this->renderSection($matches[0]);
				$content = $this->renderItems();

				return $content === false ? $matches[0] : $content;
			}, $this->layout);
		} else {
			$content = $this->renderEmpty();
		}
		$tag = ArrayHelper::remove($this->options, 'tag', 'div');
		echo Html::tag($tag, $content);
	}
	
	public function registerTranslations()
	{
		$i18n = \Yii::$app->i18n;
		$i18n->translations['@vendor/faravaghi/datatables/*'] = [
			'class' => 'yii\i18n\PhpMessageSource',
			'sourceLanguage' => 'fa',
			'basePath' => __DIR__ . '/messages',
			'fileMap' => [
				'@vendor/faravaghi/datatables/edt' => 'edt.php',
			],
		];
	}

	public static function t($category, $message, $params = [], $language = null)
	{
		return \Yii::t('@vendor/faravaghi/datatables/' . $category, $message, $params, $language);
	}

	/**
	 * Returns the options for the datatables view JS widget.
	 * @return array the options
	 */
	protected function getClientOptions()
	{
		return $this->clientOptions;
	}

	/**
	 * Renders the table body.
	 * @return string the rendering result.
	 */
	public function renderTableBody()
	{
		$clientOptions = $this->getClientOptions();

		if (isset($clientOptions['serverSide']) && $clientOptions['serverSide'] == true) {
			$colspan = count($this->columns);
			$this->emptyText = self::t('edt', 'Loading...');

			return "<tbody>\n<tr><td colspan=\"$colspan\">" . $this->renderEmpty() . "</td></tr>\n</tbody>";
		}
		else{
			return parent::renderTableBody();
		}
	}

	/**
	 * Returns formatted dataset from dataProvider in an array
	 * instead of rendering a HTML table. @see renderTableBody
	 * 
	 * @access public
	 * @param int $draw
	 * @return void
	 */
	public function getFormattedData($draw) {
		$result = array();
		/*$returnData = array();

		$returnData = array(
			'draw'			  => $draw,
			'recordsTotal'	  => $this->dataProvider->getTotalCount(),
			'recordsFiltered'   => $this->dataProvider->getTotalCount(),
			'data'			  => $result,
		);

		return $returnData;*/

		$request = \Yii::$app->request;

		$originalQuery = $this->dataProvider->query;
		$filterQuery = clone $originalQuery;
		$filterQuery->where = null;

		$search = $request->getQueryParam('search', ['value' => null, 'regex' => false]);
		$columns = $request->getQueryParam('columns', []);
		$order = $request->getQueryParam('order', []);

		$filterQuery = $this->applyFilter($filterQuery, $columns, $search);
		$filterQuery = $this->applyOrder($filterQuery, $columns, $order);

		if (!empty($originalQuery->where)) {
			$filterQuery->andWhere($originalQuery->where);
		}

		$actionQuery = clone $filterQuery;
		$this->dataProvider->query = $filterQuery;
		// echo "<pre>";print_r($actionQuery->createCommand()->sql);echo "</pre>";die();

		try {
			$models = $this->dataProvider->getModels();
			$keys = $this->dataProvider->getKeys();
			foreach ($models as $index => $model) {
				$key = $keys[$index];
				$rows = array();

				foreach($this->columns as $column) {
					$rows[] = $column->renderDataCell($model, $key, $index);
				}

				$result[] = $rows;
			}

			$response = [
				'draw' => (int)$draw,
				'recordsTotal' => (int)$originalQuery->count(),
				'recordsFiltered' => (int)$actionQuery->count(),
				'data' => $result,
			];
		}
		catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
		return $response;
	}

	/**
	 * @param ActiveQuery $query
	 * @param array $columns
	 * @param array $order
	 * @return ActiveQuery
	 */
	public function applyOrder(ActiveQuery $query, $columns, $order)
	{
		if (isset($this->applyOrder) && $this->applyOrder !== null) {
			return call_user_func($this->applyOrder, $query, $columns, $order);
		}

		foreach ($order as $key => $item) {
			$sort = $item['dir'] == 'desc' ? SORT_DESC : SORT_ASC;
			// $query->addOrderBy([$columns[$item['column']]['data'] => $sort]);
			if($this->sortableColumn[$item['column']] != NULL){
				$query->addOrderBy([$this->sortableColumn[$item['column']] => $sort]);
			}
		}

		return $query;
	}

	/**
	 * @param ActiveQuery $query
	 * @param array $columns
	 * @param array $search
	 * @return ActiveQuery
	 * @throws InvalidConfigException
	 */
	public function applyFilter(ActiveQuery $query, $columns, $search)
	{
		if (isset($this->applyFilter) && $this->applyFilter !== null) {
			return call_user_func($this->applyFilter, $query, $columns, $search);
		}

		/** @var \yii\db\ActiveRecord $modelClass */
		$modelClass = $query->modelClass;
		$schema = $modelClass::getTableSchema()->columns;
		foreach ($columns as $column) {
			if ($column['searchable'] == 'true' && array_key_exists($column['data'], $schema) !== false) {
				$value = empty($search['value']) ? $column['search']['value'] : $search['value'];
				$query->orFilterWhere(['like', $column['data'], $value]);
			}
		}

		if(!empty($this->searchableColumn)){
			foreach ($this->searchableColumn as $column) {
				$query->orFilterWhere(['like', $column, $search['value']]);
			}
		}

		return $query;
	}
}
