requirejs(['jquery', 'mage/url', 'mage/validation', 'domReady!'], function ($, url) {
    var selector_key_postcode = 'input:visible[name="company[postcode]"]';
    var selector_key_region = 'input:visible[name="company[region]"]';
    var selector_key_region_id = 'select:visible[name="company[region_id]"]';
    var selector_key_city = 'input:visible[name="company[city]"]';
    var selector_key_street = 'input:visible[name="company[street][0]"]';
    var selector_key_street_myacc = 'input:visible#street_1';

    var selector_key_suggestion_wrapper = '.suggestion_wrapper';
    var selector_key_suggestion_list = '.suggestion_list';
    var selector_key_suggestion_list_item = '.suggestion_list_item';
    var selector_key_suggestion_not_available = '.suggestion_not_available';
    var selector_key_suggestion_postcode = 'suggestion_postcode';
    var selector_key_suggestion_region = 'suggestion_region';
    var selector_key_suggestion_region_id = 'suggestion_region_id';
    var selector_key_suggestion_city = 'suggestion_city';
    var selector_key_suggestion_street = 'suggestion_street';
    var message_no_suggestion_available = $.mage.__('No suggestion available.');

    var selector_key_arr = [
        selector_key_postcode,
        selector_key_region,
        selector_key_city,
        selector_key_street,
        selector_key_street_myacc
    ];

    if (isModuleEnable()) {
        //User trigger input event on textboxes
        jQuery.each(selector_key_arr, function (i, selector_key) {
            $(document).on('keyup paste', selector_key, null, function (e) {
                var _this = this;
                var keyCode = e.keyCode;
                // Short pause to wait for paste to complete
                setTimeout(function () {
                    var el = $(_this);
                    var form = el.closest('form');
                    var val = el.val();
                    val = val.trim();
                    if ([33, 34, 35, 36, 37, 38, 39, 40, 45, 46].includes(keyCode)) {
                        return false;
                    }

                    var rexRule = /(^\d{3}-\d{2,4}$)/;
                    var s = $(el).attr('name');
                    if (s === 'company[postcode]' && rexRule.test(val) === true) {
                        val = val.replace('-', "");
                    }

                    if (window.location.href.indexOf('address') > 0 && el.attr('name') === 'company[postcode]') {
                        if (el.parent().find('.fix-zipcode').length === 0) {
                            var str = sprintf('<div class ="fix-zipcode" ></div>');
                            el.after(str);
                        }
                    }
                    if (inputValLongEngough(form, val, el)) {
                        getAndShowSuggestion(val, url, el);
                    } else {
                        removeSuggestion(el);
                    }
                }, 100);

            });
            $(document).on('blur', selector_key, null, function () {
                setTimeout(function () {
                    $(selector_key_suggestion_wrapper).hide();
                }, 500);
            });
        });


        //User chose a suggestion
        $(document).on('click', selector_key_suggestion_list_item, null, function () {
            $(selector_key_suggestion_list).hide();
            autoFillData($(this));
        });
    }

    //Show suggestion if available, otherwise show error message
    const showSuggestion = (el, suggestions) => {
        var parent = el.parent();
        var suggestion_wrapper = parent.find(selector_key_suggestion_wrapper);

        removeSuggestion(el);

        var str = sprintf('<div class="%s" ></div>', selector_key_suggestion_wrapper.substr(1));

        if (window.location.href.indexOf('address') > 0 && el.attr('name') === 'company[postcode]') {
            el.before(str);
        } else {
            el.after(str);
        }


        var suggestionContent = null;
        if (suggestions.length > 0) {
            suggestionContent = $('<ul></ul>');
            suggestionContent.addClass(selector_key_suggestion_list.substr(1));
            suggestionContent.attr("suggestion_for", el.attr('name'));

            $.each(suggestions, function (i, item) {
                var suggestion_data = {
                    postcode: item.zipcode,
                    region: item.region_name,
                    city: item.city_name,
                    street: item.town_name,
                    city_id: item.city_id,
                    region_id: item.region_id,
                    country_id: item.country_id
                };

                var suggestionItem = $('<li></li>');
                suggestionItem.attr(selector_key_suggestion_postcode, suggestion_data.postcode);
                suggestionItem.attr(selector_key_suggestion_region, suggestion_data.region);
                suggestionItem.attr(selector_key_suggestion_region_id, suggestion_data.region_id);
                suggestionItem.attr(selector_key_suggestion_city, suggestion_data.city);
                suggestionItem.attr(selector_key_suggestion_street, suggestion_data.street);
                suggestionItem.addClass(selector_key_suggestion_list_item.substr(1));
                suggestionItem.html('〒' + item.zipcode.substr(0, 3) + '-' + item.zipcode.substr(3) + item.region_name + item.city_name + item.town_name);
                suggestionItem.appendTo(suggestionContent);
            });
        } else {
            suggestionContent = $('<div></div>');
            suggestionContent.addClass(selector_key_suggestion_not_available.substr(1));
            message_no_suggestion_available = "";
            suggestionContent.html(message_no_suggestion_available);
        }
        if (window.location.href.indexOf('address') > 0) {
            $("<div class='fixbug_auto_fill_address'></div>").appendTo(parent.find(selector_key_suggestion_wrapper));
        }

        suggestionContent.appendTo(parent.find(selector_key_suggestion_wrapper));
        $(selector_key_suggestion_wrapper).hide();
        parent.find(selector_key_suggestion_wrapper).show();
    };


    function getAndShowSuggestion(val, urlBuilder, el) {
        var url = urlBuilder.build('/rest/V1/isobar-postcode/' + val);

        $.ajax(
            {
                showLoader: true,
                url: url,
                data: null,
                type: 'GET'
            }
        ).done(function (suggestions) {
            suggestions = JSON.parse(suggestions);
            showSuggestion(el, suggestions);
        });
    }


    function autoFillData(el) {
        var postcode = el.attr(selector_key_suggestion_postcode);
        var region = el.attr(selector_key_suggestion_region);
        var regionId = el.attr(selector_key_suggestion_region_id);
        var city = el.attr(selector_key_suggestion_city);
        var street = el.attr(selector_key_suggestion_street);

        var f = el.closest('form');

        f.find(selector_key_postcode).val(postcode).change();
        f.find(selector_key_postcode).parent().find('.fixbug_auto_fill_address').remove();
        f.find(selector_key_region).val(region).change();
        f.find(selector_key_region_id).val(regionId).change();
        f.find(selector_key_city).val(city).change();
        f.find(selector_key_street).val(street).change();
        f.find(selector_key_street_myacc).val(street).change();
    }

    function sprintf() {
        var args = Array.prototype.slice.call(arguments)
            , n = args.slice(1, -1)
            , text = args[0]
            , _res = isNaN(parseInt(args[args.length - 1]))
            ? args[args.length - 1]
            : Number(args[args.length - 1])
            , arr = n.concat(_res)
            , res = text;
        for (var i = 0; i < arr.length; i++) {
            res = res.replace(/%d|%s/, arr[i])
        }
        return res
    }

    function inputValLongEngough(form, val, el) {
        var s = $(el).attr('name');
        if (
            (s === 'company[postcode]' && val.length >= 5) ||
            (s === 'company[region]' && val.length >= 2) ||
            (s === 'company[city]' && val.length >= 2) ||
            (s === 'company[street][0]' && val.length >= 3)
        ) {
            if (form.find('select[name="company[country_id]"]').val() === 'JP') {
                return true;
            }
        }

        s = $(el).attr('id');
        return s === 'street_1' && val.length >= 3;
    }

    function removeSuggestion(el) {
        var parent = el.parent();
        var suggestion_wrapper = parent.find(selector_key_suggestion_wrapper);

        if (suggestion_wrapper.length > 0) {
            suggestion_wrapper.remove();
        }
    }

    function isModuleEnable() {
        return window.isModuleZicodeSuggestionEnabled;
    }
});
