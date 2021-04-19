
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="robots" content="noindex, nofollow">
    <meta name="googlebot" content="noindex">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>BankSathi CRM Login</title>
	<link rel="stylesheet" href="{{ asset('crm/assets/styles/style.min.css') }}">

	<!-- Waves Effect -->
	<link rel="stylesheet" href="{{ asset('crm/assets/plugin/waves/waves.min.css') }}">

</head>

<body>

<div id="single-wrapper">
    {{-- @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif --}}

    <form method="POST" action="{{ route('crm.login') }}" class="frm-single">
        @csrf
		<div class="inside">
            @if(session('status'))
                <div class="alert alert-success mb-1 mt-1">
                {{ session('status') }}
                </div>
            @endif
            @if ($message = Session::get('success'))
                        <div class="alert alert-success">
                            <strong>{{ $message }}</strong>
                        </div>
			@endif
			
			@if ($error = Session::get('error'))
				<div class="alert alert-warning">
					<strong>{{ $error }}</strong>
				</div>
			@endif

            @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Error!</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
			<div class="title"><strong>BankSathi</strong> CRM</div>
			<!-- /.title -->
			<div class="frm-title">{{ __('Login') }}</div>
			<!-- /.frm-title -->
			<div class="frm-input"><input placeholder="{{ __('Email') }}" class="frm-inp" id="email" type="email" name="email" :value="old('email')" required autofocus /><i class="fa fa-user frm-ico"></i></div>
			<!-- /.frm-input -->
			<div class="frm-input"><input id="password" placeholder="{{ __('Password') }}" class="frm-inp" type="password" name="password" required autocomplete="current-password" /><i class="fa fa-lock frm-ico"></i></div>
			<!-- /.frm-input -->
			<div class="clearfix margin-bottom-20">
				<div class="pull-right"><a href="#" class="a-link"><i class="fa fa-unlock-alt"></i>{{ __('Forgot your password?') }}</a></div>
				<!-- /.pull-right -->
			</div>
			<!-- /.clearfix -->
			<button type="submit" class="frm-submit">{{ __('Login') }}<i class="fa fa-arrow-circle-right"></i></button>
			<!-- /.row -->
			<div class="frm-footer">BankSathi © <?php echo date("Y");?>.</div>
			<!-- /.footer -->
		</div>
		<!-- .inside -->
	</form>
	<!-- /.frm-single -->
</div><!--/#single-wrapper -->

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="{{ asset('crm/assets/script/html5shiv.min.js') }}"></script>
		<script src="{{ asset('crm/assets/script/respond.min.js') }}"></script>
	<![endif]-->
	<!--
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="{{ asset('crm/assets/scripts/jquery.min.js') }}"></script>
	<script src="{{ asset('crm/assets/scripts/modernizr.min.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/bootstrap/js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/nprogress/nprogress.js') }}"></script>
	<script src="{{ asset('crm/assets/plugin/waves/waves.min.js') }}"></script>

	<script src="{{ asset('crm/assets/scripts/main.min.js') }}"></script>
</body>
</html>
