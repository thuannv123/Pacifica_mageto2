define(["jquery"], function ($) {
    var mod = {};
    mod.init = function (orderId) {
        if (!orderId) {
            return;
        }

        $(document).ready(function () {
            var requestResult = function () {
                $.ajax({
                    url: `/atome/payment/resultApi?orderId=${orderId}`,
                    success: function (res) {
                        if (res.refresh) {
                            window.location.reload();
                        } else {
                            setTimeout(requestResult, 1000)
                        }
                    }
                });
            }

            requestResult();
        });
    };

    return mod;
});

