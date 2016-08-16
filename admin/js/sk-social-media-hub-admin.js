(function ($) {
    'use strict';

    $(function () {

        $('a#get-twitter-token').on('click', function () {

            var trigger_btn = $(this);

            trigger_btn.attr('disabled', 'disabled'); // disable button during ajax call
            trigger_btn.before('<span id="stc-spinner" class="spinner" style="display:inline"></span>'); // adding spinner element
            $('#message').remove(); // remove any previous message

            var data = {
                action: 'get_twitter_token',
                nonce: ajax_object.ajax_nonce
            };

            $.post(ajax_object.ajaxurl, data, function (response) {

                if (response.access_token.length > 0) {
                    console.log(response.access_token);
                    trigger_btn.closest('td').find('#twitter_access_token').val(response.access_token);
                } else {
                    console.log('fel');
                }

                trigger_btn.attr('disabled', false); // enable button

            }).error(function () {
                alert("Problem calling: " + data.action + "\nCode: " + this.status + "\nException: " + this.statusText);
            });

            return false;

        });

        $('a#get-facebook-token').on('click', function () {

            var trigger_btn = $(this);

            trigger_btn.attr('disabled', 'disabled'); // disable button during ajax call
            trigger_btn.before('<span id="stc-spinner" class="spinner" style="display:inline"></span>'); // adding spinner element
            $('#message').remove(); // remove any previous message

            var data = {
                action: 'get_facebook_token',
                nonce: ajax_object.ajax_nonce
            };

            $.post(ajax_object.ajaxurl, data, function (response) {

                if (response.access_token !== undefined && response.access_token.length > 0) {
                    console.log(response.access_token);
                    trigger_btn.closest('td').find('#facebook_access_token').val(response.access_token);
                } else {
                    trigger_btn.closest('td').append('<div class="error">' + response.error.message + '</div>');
                    console.log('fel');
                }

                trigger_btn.attr('disabled', false); // enable button

            }).error(function () {
                alert("Problem calling: " + data.action + "\nCode: " + this.status + "\nException: " + this.statusText);
            }, 'json');

            return false;

        });


    });


})(jQuery);
