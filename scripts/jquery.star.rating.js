/**
 * jQuery Star Rating plugin
 * Joost van Velzen - http://joost.in
 *
 * v 1.0.3
 *
 * cc - attribution + share alike
 * http://creativecommons.org/licenses/by-sa/4.0/
 */

(function ($) {
    $.fn.addRating = function (options) {
        var obj = this;
        var settings = $.extend({
            max: 5,
            half: true,
            fieldName: 'rating',
            fieldId: 'rating',
            icon: 'star',
            selectedRatings:0
        }, options);
        this.settings = settings;

        // create the stars
        for (var i = 1; i <= settings.max; i++) {
            var star = $('<i/>').addClass('material-icons').html(this.settings.icon + '_border').data('rating', i).appendTo(this).click(
                function () {
                    obj.setRating($(this).data('rating'));
                }
            ).hover(
                function (e) {
                    obj.showRating($(this).data('rating'), false);
                }, function () {
                    obj.showRating(0, false);
                }
            );

        }
        $(this).append('<input type="hidden" name="' + settings.fieldName + '" id="' + settings.fieldId + '" value="' + settings.selectedRatings + '" />');

        obj.showRating(settings.selectedRatings, true);
    };

    $.fn.setRating = function (numRating) {
        var obj = this;
        $('#' + obj.settings.fieldId).val(numRating);
        obj.showRating(numRating, true);
    };

    $.fn.showRating = function (numRating, force) {
        var obj = this;
        if ($('#' + obj.settings.fieldId).val() == '' || force) {
            $(obj).find('i').each(function () {
                var icon = obj.settings.icon + '_border';
                $(this).removeClass('selected');

                if ($(this).data('rating') <= numRating) {
                    icon = obj.settings.icon;
                    $(this).addClass('selected');
                }
                $(this).html(icon);
            })
        }
    }

}(jQuery));