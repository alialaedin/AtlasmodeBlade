<!DOCTYPE html>
<html lang="en" dir="rtl">
	<head>

		<!-- Meta data -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta content="DayOne - It is one of the Major Dashboard Template which includes - HR, Employee and Job Dashboard. This template has multipurpose HTML template and also deals with Task, Project, Client and Support System Dashboard." name="description">
		<meta content="Spruko Technologies Private Limited" name="author">
		<meta name="keywords" content="admin dashboard, admin panel template, html admin template, dashboard html template, bootstrap 4 dashboard, template admin bootstrap 4, simple admin panel template, simple dashboard html template,  bootstrap admin panel, task dashboard, job dashboard, bootstrap admin panel, dashboards html, panel in html, bootstrap 4 dashboard"/>

		<!-- Title -->
		<title>{{ \Modules\Setting\Entities\Setting::getFromName('title') }}</title>
		<!--Favicon -->
		<link href="{{asset('assets/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet" />
		<link rel="stylesheet" href="{{asset('assets/plugins/summernote/summernote-bs4.css')}}">
		<!-- Style css -->
		<link href="{{asset('assets/css-rtl/style.css')}}" rel="stylesheet" />
		<link href="{{asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet" />
		<!-- INTERNAL Data table css -->
		<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap4.min-rtl.css')}}" rel="stylesheet" />
		<link href="{{asset('assets/FontAwesome/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" />
		<link href="{{asset('assets/css-rtl/style-rtl.css')}}" rel="stylesheet" />
		<link href="{{asset('assets/css/fonts.css')}}" rel="stylesheet"/>
	</head>
	<body>
		<div class="page login-bg">
			<div class="page-single">
				<div class="container">
					<div class="row">
						<div class="col mx-auto">
							<div class="row justify-content-center">
								<div class="col-md-7 col-lg-5">
									<div class="card">
										<div class="p-4 pt-6 text-center">
											<p class="mb-2" style="font-size: 30px">ورود</p>
											<p class="text-muted">به اکانت خود وارد شوید</p>
										</div>
                    <x-alert-danger></x-alert-danger>
										<x-alert-success></x-alert-success>
										<form class="card-body pt-3" action="{{route('admin.login')}}" id="login" name="login" method="post">
											@csrf
											<div class="form-group">
												<label class="form-label">نام کاربری</label>
												<input class="form-control" placeholder="نام کاربری را وارد کنید" name="username" type="text" value="{{old('username')}}" autofocus required>
											</div>
											<div class="form-group">
												<label class="form-label">رمز عبور</label>
												<input class="form-control" placeholder="رمز عبور را وارد کنید" name="password" type="password"  required>
											</div>
											<div class="submit">
												<button class="btn btn-primary btn-block">ورود</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
        	<!-- Jquery js-->
		<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>

		<!-- Bootstrap4 js-->
		<script src="{{asset('assets/plugins/bootstrap/popper.min.js')}}"></script>
		<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

		<!-- Select2 js -->
		<script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>

		<!-- P-scroll js-->
		<script src="{{asset('assets/plugins/p-scrollbar/p-scrollbar.js')}}"></script>

		<!-- Custom js-->
    	</body>
</html>
