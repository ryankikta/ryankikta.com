(function ($) {
	'use strict';
	$(window)
		.load(function () {
			$('.add_more_fact')
				.click(function () {
					$(this)
						.parents('table')
						.prev()
						.append('<tr>\
					<td><input type="text" name="more_fact_label[]"></td>\
					<td><input type="text" name="more_fact_value[]"></td>\
				</tr>');
				})
			$('.Add_New_Fact')
				.click(function () {
					$('.fact_modal_bg')
						.show();
					return false;
				})
			$('.fact_modal_header > h3> span')
				.click(function () {
					$('.fact_modal_bg')
						.hide();
				})
			$('.delete_fact_key')
				.click(function () {
					var base = $(this)
					$.post(ajaxurl, {
							action: 'delete_fact_key',
							'type': base.data('type'),
							'key': base.data('key'),
							'post_id': base.data('id'),
						})
						.done(function (data) {
							$('.fact_modal_bg')
						.hide();
							base.parent()
								.remove();
						})
						.fail(function (data) {});
				})
			$(document)
				.on('click', '.Add_New_Fact_Save', function () {
					var base = $(this);
					var event_type = $('.event_type')
								.val()
					$.post(ajaxurl, {
							action: 'add_new_fact',
							'post_id': $('.event_post_id')
								.val(),
							'type': $('.event_type')
								.val(),
							'date': $('.event_date')
								.val(),
							'place': $('.event_place')
								.val(),
						})
						.done(function (data) {

							$('.fact_modal_bg')
						.hide();

							$('div.cont-'+event_type).append(data);
							if(!$('div.cont-'+event_type).length) {
								window.location.reload();
							}

						})
						.fail(function (data) {

						});
					return false;
				})
		});
})(jQuery);