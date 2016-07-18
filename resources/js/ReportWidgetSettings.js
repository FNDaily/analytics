/**
 * Report Widget Settings
 */
Analytics.ReportWidgetSettings = Garnish.Base.extend(
{
	$container: null,
	$form: null,
	$chartTypes: null,
	$chartSelect: null,
	$selectizeSelects: null,

	init: function(id, settings)
	{
		this.$container = $('#'+id);
		this.$form = this.$container.closest('form');

		this.$chartTypes = $('.chart-picker ul.chart-types li', this.$form);
		this.$chartSelect = $('.chart-select select', this.$form);

		this.$selectizeSelects = $('.selectize select', this.$form);
		this.$selectizeSelects.selectize();

		this.addListener(this.$chartTypes, 'click', $.proxy(function(ev) {

			var $target = $(ev.currentTarget);

			this.$chartTypes.removeClass('active');

			$target.addClass('active');

			this.$chartSelect.val($target.data('chart-type'));
			this.$chartSelect.trigger('change');

		}, this));

		this.$chartTypes.filter('[data-chart-type='+this.$chartSelect.val()+']').trigger('click');

		window.dashboard.grid.refreshCols(true);
	}
});

