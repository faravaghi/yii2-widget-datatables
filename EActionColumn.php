<?php
/**
 * @copyright dadeh kavi rezvan Co.
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-widget-datatables
 */
namespace faravaghi\datatables;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;

use yii\grid\ActionColumn;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 *
 * To add an ActionColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *	 // ...
 *	 [
 *		 'class' => ActionColumn::className(),
 *		 // you may configure additional properties here
 *	 ],
 * ]
 * ```
 *
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 * @since 2.0
 */
class EActionColumn extends ActionColumn
{

	/**
	 * Initializes the default button rendering callbacks.
	 */
	protected function initDefaultButtons()
	{
		if (!isset($this->buttons['view'])) {
			$this->buttons['view'] = function ($url, $model, $key) {
				$options = array_merge([
					'class' => 'view-link hint--top hint--rounded hint--success',
					'title' => Yii::t('yii', 'View'),
					'data-hint' => Yii::t('yii', 'View'),
					'data-toggle' => 'modal',
					'data-target' => '#view-modal',
					'data-pjax' => '0',
				], $this->buttonOptions);
				return Html::a('<span class="glyphicon glyphicon-eye-open font-green"></span>', $url, $options);
			};
		}
		if (!isset($this->buttons['update'])) {
			$this->buttons['update'] = function ($url, $model, $key) {
				$options = array_merge([
					'class' => 'hint--top hint--rounded hint--warning',
					'title' => Yii::t('yii', 'Update'),
					'data-hint' => Yii::t('yii', 'Update'),
					'data-pjax' => '0',
				], $this->buttonOptions);
				return Html::a('<span class="glyphicon glyphicon-pencil font-yellow"></span>', $url, $options);
			};
		}
		if (!isset($this->buttons['delete'])) {
			$this->buttons['delete'] = function ($url, $model, $key) {
				$options = array_merge([
					'class' => 'hint--top hint--rounded hint--error',
					'title' => Yii::t('yii', 'Delete'),
					'data-hint' => Yii::t('yii', 'Delete'),
					'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
					'data-method' => 'post',
					// 'data-callback' => 
					'data-pjax' => '1',
				], $this->buttonOptions);
				return Html::a('<span class="glyphicon glyphicon-trash font-red-pink"></span>', $url, $options);
			};
		}
	}
}
