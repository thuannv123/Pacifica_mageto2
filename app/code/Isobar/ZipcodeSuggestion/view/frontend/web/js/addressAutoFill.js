requirejs(['jquery', 'mage/url', 'mage/validation', 'domReady!'], function ($, url) {
    var selector_key_postcode = 'input:visible[name="postcode"]';
    var selector_key_region = 'input:visible[name="region"]';
    var selector_key_region_id = 'select:visible[name="region_id"]';
    var selector_key_district = 'input:visible[name="city"]';
    var selector_key_subdistrict_checkout = 'input:visible[name="custom_attributes[subdistrict]"]';
    var selector_key_subdistrict_address = 'input:visible[name="subdistrict"]';

    var selector_key_suggestion_wrapper = '.suggestion_wrapper';
    var selector_key_suggestion_list = '.suggestion_list';
    var selector_key_suggestion_list_item = '.suggestion_list_item';
    var selector_key_suggestion_not_available = '.suggestion_not_available';
    var selector_key_suggestion_postcode = 'suggestion_postcode';
    var selector_key_suggestion_region = 'suggestion_region';
    var selector_key_suggestion_region_id = 'suggestion_region_id';
    var selector_key_suggestion_district = 'suggestion_district';
    var selector_key_suggestion_subdistrict = 'suggestion_subdistrict';
    var message_no_suggestion_available = $.mage.__('No suggestion available.');

    var selector_key_arr = [
        selector_key_postcode,
        selector_key_region,
        selector_key_district,
        selector_key_subdistrict_checkout,
        selector_key_subdistrict_address
    ];

    var suggestionCountry = getSuggestionCountry();
    //Check module enable end suggestion country
    if (isModuleEnable()) {
        changeCityLabelThaiLand(suggestionCountry);
        //User trigger input event on textboxes
        jQuery.each(selector_key_arr, function (i, selector_key) {
            $(document).on('keyup paste input', selector_key, null, function (e) {
                var _this = this;
                var keyCode = e.keyCode;

                setTimeout(function () {
                    var el = $(_this);
                    var form = el.closest('form');
                    var val = el.val();

                    val = val.trim();

                    if (window.location.href.indexOf('address') > 0 && el.attr('name') === 'postcode') {
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
    function showSuggestion(el, suggestions) {
        var parent = el.parent();

        removeSuggestion(el);

        var str = sprintf('<div class="%s" ></div>', selector_key_suggestion_wrapper.substr(1));

        if (window.location.href.indexOf('address') > 0 && el.attr('name') === 'postcode') {
            el.before(str);
        } else {
            el.after(str);
        }

        var suggestionContent = null;

        if (suggestions.length > 0) {
            suggestionContent = $('<ul></ul>');
            suggestionContent.addClass(selector_key_suggestion_list.substr(1));
            suggestionContent.attr("suggestion_for", el.attr('name'));

            var districtId = '';
            $.each(suggestions, function (i, item) {
                if(districtId !== item.district_id) {
                    var suggestion_data = {
                        postcode: item.zipcode,
                        region: item.region_name,
                        district: item.district_name,
                        subdistrict: item.subdistrict_name,
                        district_id: item.district_id,
                        region_id: item.region_id,
                        country_id: item.country_id
                    };

                    var suggestionItem = $('<li></li>');

                    suggestionItem.attr(selector_key_suggestion_postcode, suggestion_data.postcode);
                    suggestionItem.attr(selector_key_suggestion_region, suggestion_data.region);
                    suggestionItem.attr(selector_key_suggestion_region_id, suggestion_data.region_id);
                    suggestionItem.attr(selector_key_suggestion_district, suggestion_data.district);
                    suggestionItem.attr(selector_key_suggestion_subdistrict, suggestion_data.subdistrict);
                    suggestionItem.addClass(selector_key_suggestion_list_item.substr(1));
                    suggestionItem.html(item.zipcode + ', ' + item.region_name + ', ' + item.district_name + ', ' + item.subdistrict_name);
                    suggestionItem.appendTo(suggestionContent);

                }
            });
        } else {
            suggestionContent = $('<div></div>');
            suggestionContent.addClass(selector_key_suggestion_not_available.substr(1));
            message_no_suggestion_available = "Zip code does not exist.";
            suggestionContent.html(message_no_suggestion_available);
        }
        //Fix bug for my address page: conflict between validate and autofill js
        if (window.location.href.indexOf('address') > 0) {
            $("<div class='fixbug_auto_fill_address'></div>").appendTo(parent.find(selector_key_suggestion_wrapper));
        }

        suggestionContent.appendTo(parent.find(selector_key_suggestion_wrapper));
        $(selector_key_suggestion_wrapper).hide();
        parent.find(selector_key_suggestion_wrapper).show();
    }

    function getAndShowSuggestion(val, urlBuilder, el) {
        var url = urlBuilder.build('/rest/V1/isobar-postcode/' + val);//40001

        $.ajax(
            {
                showLoader: true,
                url: url,
                data: null,
                type: 'GET',
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
        var district = el.attr(selector_key_suggestion_district);
        var subdistrict = el.attr(selector_key_suggestion_subdistrict);
        var f = el.closest('form');

        f.find(selector_key_postcode).val(postcode).change();
        f.find(selector_key_postcode).parent().find('.fixbug_auto_fill_address').remove();
        f.find(selector_key_region).val(region).change();
        f.find(selector_key_region_id).val(regionId).change();
        f.find(selector_key_district).val(district).change();
        f.find(selector_key_subdistrict_checkout).val(subdistrict).change();
        f.find(selector_key_subdistrict_address).val(subdistrict).change();
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
            res = res.replace(/%d|%s/, arr[i]);
        }
        return res;
    }


    function inputValLongEngough(form, val, el) {
        var s = $(el).attr('name');

        if (
            (s === 'postcode' && val.length === 5) ||
            (s === 'region' && val.length >= 2) ||
            (s === 'district' && val.length >= 2)
        ) {
            if (form.find('select[name="country_id"]').val() === suggestionCountry) {
                changeCityLabelThaiLand(suggestionCountry);
                return true;
            }
        }

        s = $(el).attr('id');
        return s === 'district' && val.length >= 3;
    }

    function removeSuggestion(el) {
        var parent = el.parent();
        var suggestion_wrapper = parent.find(selector_key_suggestion_wrapper);

        if (suggestion_wrapper.length > 0) {
            suggestion_wrapper.remove();
        }
    }

    function changeCityLabelThaiLand(suggestionCountry) {
        var existCondition = setInterval(function() {
            if ($('form').find('select[name="country_id"]').length) {
                clearInterval(existCondition);
                var country_id = $('form').find('select[name="country_id"]').val();

                if(suggestionCountry === 'TH' && country_id === 'TH'){
                    $('.city label span').text(window.lableTHDistrict);
                }
            }
        }, 100); // check every 100ms
    }

    function isModuleEnable() {
        return window.isModuleZicodeSuggestionEnabled;
    }

    function getSuggestionCountry() {
        return window.suggestionCountry;
    }
});
