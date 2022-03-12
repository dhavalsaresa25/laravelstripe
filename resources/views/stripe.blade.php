<!DOCTYPE html>
<html>
<head>
	<title>Laravel 5 - Stripe Payment Gateway Integration Example - ItSolutionStuff.com</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style type="text/css">
        .panel-title {
        display: inline;
        font-weight: bold;
        }
        .display-table {
            display: table;
        }
        .display-tr {
            display: table-row;
        }
        .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 61%;
        }
    </style>
</head>
<body>

<div class="container">

    <h1></h1>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default credit-card-box">
                <div class="panel-heading display-table" >
                    <div class="row display-tr" >
                        <h3 class="panel-title display-td" >Payment Details</h3>
                        <div class="display-td" >
                            {{-- <img class="img-responsive pull-right" src="http://i76.imgup.net/accepted_c22e0.png"> --}}
                        </div>
                    </div>
                </div>
                <div class="panel-body">

                    <form id="payment" name="paymentForm" onsubmit="return false" method="POST">
                        @csrf
                        <div class='form-row row'>
                            <div class='col-xs-12 col-md-6 form-group err_first_name'>
                                <label class='control-label'>First Name</label>
                                <input autocomplete='off' name="first_name" class='form-control card-cvc' placeholder='' type='text'>
                            </div>
                            <div class='col-xs-12 col-md-6 form-group err_last_name'>
                                <label class='control-label'>Last Name</label>
                                <input autocomplete='off' name="last_name" class='form-control card-cvc' placeholder='' type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 form-group err_email'>
                                <label class='control-label'>Email</label>
                                <input class='form-control' name="email" type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 form-group err_name_on_card'>
                                <label class='control-label'>Name on Card</label>
                                <input class='form-control' name="name_on_card" size='4' type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 form-group card required err_card_number'>
                                <label class='control-label'>Card Number</label>
                                <input autocomplete='off' name="card_number" class='form-control card-number' size='20' value="4242424242424242" type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-xs-12 col-md-4 form-group cvc err_CVC'>
                                <label class='control-label'>CVC</label>
                                <input autocomplete='off' name="CVC" class='form-control card-cvc' placeholder='ex. 311' size='4' value="314" type='text'>
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration err_expiration_month'>
                                <label class='control-label'>Expiration Month</label>
                                <input class='form-control card-expiry-month' name="expiration_month" placeholder='MM' size='2' type='text' value="8">
                            </div>
                            <div class='col-xs-12 col-md-4 form-group expiration err_expiration_year'>
                                <label class='control-label'>Expiration Year</label>
                                <input class='form-control card-expiry-year' name="expiration_year" placeholder='YYYY' size='4' type='text' value="2022">
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-md-12 error form-group hide'>
                                <div class='alert-danger alert'>Please correct the errors and try
                                    again.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-lg btn-block" id="payment_submit" type="submit">Pay Now ($100)</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    $('#payment_submit').on('click', function(e) {
        $.ajaxSetup({
            headers:
            {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var form = document.paymentForm;
        var formData = new FormData(form);
        var url = '{{ route('stripe.store') }}';
        $.ajax({
            type: 'POST',
            url: url,
            processData: false,
            contentType: false,
            dataType: 'json',
            data: formData,
            dataSrc: "",
            beforeSend: function ()
            {
                $('span.alerts').remove();
                $("div#divLoading").addClass('show');
            },
            complete: function (data, status)
            {
                $("div#divLoading").removeClass('show');
                // if (status.indexOf('error') > -1)
                // {
                //     showSwalSomethingGoesWrong();
                // }
            },
            success: function (data)
            {
                if (data.status == 401)
                {
                    $.each(data.error1, function (index, value) {
                        $('.err_' + index).append('<span class="small alerts text-danger">' + value + '</span>');
                    });
                }

                if (data.status == 500){
                }

                if (data.status == 200){

                }
            }
        });
    });
</script>
</html>
