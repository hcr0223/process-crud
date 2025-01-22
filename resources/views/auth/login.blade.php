@extends('layout.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-md-4">
			<div class="card mt-5">
				<div class="card-header">Iniciar Sesión</div>
				<div class="card-body">
					<form method="POST" action="{{ route('login') }}">
						{{ csrf_field() }}
						<div class="mb-3">
							<label class="form-label">Correo:</label>
							<input type="email" name="email" id="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}">
							@if ($errors->has('email'))
							<div class="invalid-feedback">
								{{ $errors->first('email') }}
							</div>
							@endif
						</div>
						<div class="mb-3">
							<label class="form-label">Contraseña:</label>
							<input type="password" name="password" id="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}">
							@if ($errors->has('password'))
							<div class="invalid-feedback">
								{{ $errors->first('password') }}
							</div>
							@endif
						</div>
						<div class="mb-3 d-flex justify-content-end">
							<button class="btn btn-primary" type="submit">Iniciar Sesión</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection