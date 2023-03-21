(function ($) {
	function calcCurrentPage(oSettings) {
		return Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength) + 1;
	}

	function calcPages(oSettings) {
		return Math.ceil(oSettings.fnRecordsDisplay() / oSettings._iDisplayLength);
	}

    $.fn.dataTableExt.oPagination.input = {
		'fnInit': function (oSettings, nPaging, fnCallbackDraw) {

            var nPagination = document.createElement('ul');
            nPagination.className = "pagination";

            var nLi = document.createElement('li');
			var nInput = document.createElement('input');
			var nTotal = document.createElement('span');
			var nInfo = document.createElement('span');

			var language = oSettings.oLanguage.oPaginate;
			var info = language.info || 'Page _INPUT_ of _TOTAL_';

			nInput.className = 'paginate_input form-control';
			nTotal.className = 'paginate_total';

			if (oSettings.sTableId !== '') {
				nPaging.setAttribute('id', oSettings.sTableId + '_ paginate');
			}

			nInput.type = 'text';

			info = info.replace(/_INPUT_/g, '</span>' + nInput.outerHTML + '<span>');
			info = info.replace(/_TOTAL_/g, '</span>' + nTotal.outerHTML + '<span>');

            nInfo.innerHTML = '<span>' + info + '</span>';

            $(nInfo).children().each(function (i, n) {
			    nLi.appendChild(n);
			});

            nPagination.appendChild(nLi);
            nPaging.appendChild(nPagination);

			$(nPaging).find('.paginate_input').keyup(function (e) {
				// 38 = up arrow
				if (e.which === 38) {
					this.value++;
				}
				// 40 = down arrow
				else if ((e.which === 40) && this.value > 1) {
					this.value--;
				}

                if (calcCurrentPage(oSettings) == this.value) return;

				if (this.value === '' || this.value.match(/[^0-9]/)) {
					/* Nothing entered or non-numeric character */
					this.value = this.value.replace(/[^\d]/g, ''); // don't even allow anything but digits
					return;
				}

				var iNewStart = oSettings._iDisplayLength * (this.value - 1);
				if (iNewStart < 0) {
					iNewStart = 0;
				}
				if (iNewStart >= oSettings.fnRecordsDisplay()) {
					iNewStart = (Math.ceil((oSettings.fnRecordsDisplay()) / oSettings._iDisplayLength) - 1) * oSettings._iDisplayLength;
				}

				oSettings._iDisplayStart = iNewStart;
				fnCallbackDraw(oSettings);
			});

			// If we can't page anyway, might as well not show it.
			var iPages = calcPages(oSettings);
			if (iPages <= 1) {
				$(nPaging).hide();
			}
		},

		'fnUpdate': function (oSettings) {
			if (!oSettings.aanFeatures.p) {
				return;
			}

			var iPages = calcPages(oSettings);
			var iCurrentPage = calcCurrentPage(oSettings);

			var an = oSettings.aanFeatures.p;
			if (iPages <= 1) // hide paging when we can't page
			{
				$(an).hide();
				return;
			}

			$(an).show();

			// Paginate of N pages text
			$(an).find('.paginate_total').html(iPages);

			// Current page number input value
			$(an).find('.paginate_input').val(iCurrentPage);
		}
	};
})(jQuery);