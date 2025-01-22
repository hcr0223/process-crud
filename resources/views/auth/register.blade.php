@extends('layout.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-md-4">
			<div class="card mt-5">
				<div class="card-header">Registro</div>
				<div class="card-body">
					<form method="POST" action="{{ route('register.create') }}">
						{{ csrf_field() }}
						<div class="mb-3">
							<label class="form-label">Nombre:</label>
							<input type="text" name="name" id="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}">
							@if($errors->has('name'))
							<div class="invalid-feedback">
								{{ $errors->first('name') }}
							</div>
							@endif
						</div>
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
						<div class="mb-3">
							<label class="form-label">Confirma Contraseña:</label>
							<input type="password" name="password_confirmation" id="password_confirmation" class="form-control {{ $errors->has('password_confirmation') }}">
							@if ($errors->has('password_confirmation'))
							<div class="invalid-feedback">
								{{ $errors->first('password_confirmation') }}
							</div>
							@endif
						</div>
						<div class="mb-3 d-flex justify-content-end">
							<button class="btn btn-primary" type="submit">Registrar</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
@endsection