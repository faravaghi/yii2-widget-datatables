<?php
/**
 * @copyright dadeh kavi rezvan Co.
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 * @package yii2-widget-datatables
 */
namespace faravaghi\datatables;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

use yii\grid\CheckboxColumn;

/**
 * ECheckboxColumn displays a column of checkboxes in a grid view.
 *
 * To add a CheckboxColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *	 // ...
 *	 [
 *		 'class' => 'faravaghi\datatables\ECheckBoxColumn',
 *		 // you may configure additional properties here
 *	 ],
 * ]
 * ```
 *
 * Users may click on the checkboxes to select rows of the grid. The selected rows may be
 * obtained by calling the following JavaScript code:
 *
 * ```javascript
 * var keys = table.$('input[type="checkbox"]').serialize();
 * // keys is an array consisting of the keys associated with the selected rows
 * ```
 *
 * @author Mohammad Ebrahim Amini <info@faravaghi.ir>
 * @since 1.0
 */
class ECheckBoxColumn extends CheckboxColumn
{
    /**
     * Renders the header cell content.
     * The default implementation simply renders [[header]].
     * This method may be overridden to customize the rendering of the header cell.
     * @return string the rendering result
     */
	protected function renderHeaderCellContent()
	{
		$name = $this->name;
		if (substr_compare($name, '[]', -2, 2) === 0) {
			$name = substr($name, 0, -2);
		}
		if (substr_compare($name, ']', -1, 1) === 0) {
			$name = substr($name, 0, -1) . '_all]';
		} else {
			$name .= '_all';
		}

		$id = $this->grid->options['id'];

$_allSelectJs = <<< JS
	$('.select-on-check-all').on('click', function(){
		var rows = table.rows({ 'search': 'applied' }).nodes();
		$('input[type="checkbox"]', rows).prop('checked', this.checked);
	});

	$('#$id tbody').on('change', 'input[type="checkbox"]', function(){
		if(!this.checked){
			var el = $('.select-on-check-all').get(0);
			if(el && el.checked && ('indeterminate' in el)){
				el.indeterminate = true;
			}
		}
	});
JS;

		$this->grid->getView()->registerJs($_allSelectJs);

		if ($this->header !== null || !$this->multiple) {
			return parent::renderHeaderCellContent();
		} else {
			return Html::checkBox($name, false, ['class' => 'select-on-check-all']);
		}
	}
}