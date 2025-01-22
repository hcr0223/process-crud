@extends('layout.app')

@section('content')
	<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script type="text/javascript" src="{{ asset('js/datatables.js') }}"></script>
	<div class="row justify-content-center">
		<div class="col-md-6">
			<div class="card mt-5">
				<div class="card-header d-flex justify-content-between align-items-center">
					<span>Tareas</span>
					<button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#taskModal"><i class="bi bi-plus-lg"></i></button>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-striped table-bordered" id="taskTable" width="100%">
							<thead>
								<tr>
									<th>ID</th>
									<th>Nombre</th>
									<th>Descripción</th>
									<th>Fecha Inicio</th>
									<th>Fecha Fin</th>
									<th>Status</th>
									<th>Acciones</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Crear / Editar Tarea</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" id="taskForm">
						@csrf
						<input type="hidden" name="id" id="id" value="">
						<input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id}}">
						<input type="hidden" name="status" id="status" value="">
						<div class="mb-3">
							<label class="form-label">Nombre:</label>
							<input type="text" name="name" id="name" class="form-control">
						</div>
						<div class="mb-3">
							<label class="form-label">Fecha de Inicio:</label>
							<input type="date" name="start_date" id="start_date" class="form-control">
						</div>
						<div class="mb-3">
							<label class="form-label">Fecha de Fin:</label>
							<input type="date" name="end_date" id="end_date" class="form-control">
						</div>
						<div class="mb-3">
							<label class="form-label">Descripción</label>
							<textarea name="description" id="description" cols="40" rows="10" class="form-control"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="sendDataBtn">Enviar</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="taskFileModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5">Archivos</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" id="taskFileForm">
						@csrf
						<input type="hidden" name="task_id" id="task_id" value="">
						<div class="input-group mb-3">
							<input type="file" name="file" id="file" class="form-control" accept=".jpg,.pdf">
							<button class="btn btn-outline-secondary" type="button" onclick="uploadFile()">Cargar</button>
						</div>
					</form>
					<hr>
					<div class="table-responsive">
						<table class="table table-striped table-bordered" id="fileTable" width="100%">
							<thead>
								<tr>
									<th>Archivo</th>
									<th>Acciones</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancelar</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		var files = [];
		document.addEventListener('DOMContentLoaded', function() {
			const dtTable = new DataTable('#taskTable', {
				language: {
					url: "{{ asset('js/spanish.json') }}"
				},
				autoWidth: true,
				ajax: {
					url: "{{ route('tasks.list') }}",
					method: 'POST',
					cache: false,
					data: {
						'_token':'{{csrf_token()}}'
					},
					dataSrc:'data'
				},
				responsive: true,
				rowId: 'id',
				columns: [
					{data: 'id'},
					{data: 'name'},
					{data: 'description'},
					{data: 'start_date'},
					{data: 'end_date'},
					{data: 'status', render: (data, type, row, meta) => {
						if(row.status) {
							return "<span class='badge rounded-pill text-bg-success'>Activo</span>"
						} else {
							return "<span class='badge rounded-pill text-bg-danger'>Inactivo</span>"
						}
					}},
					{data: null, orderable: false}
				],
				columnDefs: [
					{
						targets: 6, render: (data, type, row, meta) => {
							let buttons = `<button type="button" class="btn btn-primary btn-sm rounded-circle" onclick="uploadFileModal(${row.id})"><i class="bi bi-files" title="Ver Archivos"></i></button>`
							if(row.status == 1){
								buttons += ` <button type="button" class="btn btn-primary btn-sm rounded-circle" title="Editar Tarea" onclick="editTask(${row.id})"><i class="bi bi-pencil-square"></i></button> <button type="button" class="btn btn-danger btn-sm rounded-circle" title="Cerrar Tarea" onclick="closeTask(${row.id})"><i class="bi bi-x-circle"></i></button>`
							}

							return buttons
						}
					}
				]
			})

			const dtFileTable = new DataTable("#fileTable", {
				language: {
					url: "{{ asset('js/spanish.json') }}"
				},
				destroy: true,
				autoWidth: true,
				responsive: true,
				rowId: 'id',
				columns: [
					{data: 'filename'},
					{data: null, orderable: false},
				],
				columnDefs: [
					{
						targets: 1, render: (data, type, row, meta) => {
							return `<a href="${row.path}" class="btn btn-primary btn-sm rounded-circle" target="_blank"><i class="bi bi-eye-fill"></i></a> <button class="btn btn-danger btn-sm rounded-circle" onclick="deleteFile(${row.id},${row.task_id})"><i class="bi bi-trash-fill"></i></button>`
						}
					}
				]
			})
		})

		$("#sendDataBtn").on('click', (e) => {
			axios.post(`{{ route('tasks.create') }}`, $('#taskForm').serialize())
			.then(res => {
				if (res.data.success) {
					$("#taskTable").DataTable().ajax.reload()
					$('#taskModal').modal('hide');
					Swal.fire({
						icon: 'success',
						title: 'Éxito!',
						html: res.data.msg
					})
				} else {
					let msg = '';
					for(val of res.data.msg) {
						msg += `<p>${val}</p>`
					}
					Swal.fire({
						icon: 'error',
						title: 'Oops',
						html: msg
					})
				}
			})
			.catch(err => console.log(err))
		})
		
		$("#taskModal").on('hidden.bs.modal', function(){
			$("#taskForm").trigger('reset')
		})

		function editTask(id) {
			const data = $("#taskTable").DataTable().row(`#${id}`).data()
			$('#taskModal').modal('show');
			for(let key in data){
				$(`#taskForm [name=${key}]`).val(data[key]);
			}
		}

		function closeTask(id) {
			Swal.fire({
				title: 'Estas seguro que quieres cerrar la tarea?',
				text: 'Esta acción no se puede revertir',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Cierralo',
				cancelButtonText: 'Cancelar',
			})
			.then((result) => {
				if (result.isConfirmed) {
					axios.post(`{{ route('tasks.update') }}`, {id: id})
					.then(res => {
						Swal.fire({
							icon: 'success',
							text: res.data.msg,
							title: 'Éxito'
						})
						$("#taskTable").DataTable().ajax.reload()
					})
					.catch(err => console.log(err))
				}
			})
		}

		function uploadFileModal(id) {
			$("#taskFileForm [name=task_id]").val(id)
			$("#taskFileModal").modal('show');
			const data = $("#taskTable").DataTable().row(`#${id}`).data()
			$("#fileTable").DataTable().clear().rows.add(data.files).draw(false)
		}

		function uploadFile() {
			var file = $("input#file");
			var task = $("input#task_id").val()
			var form = new FormData;
			form.append('file', file[0].files[0])
			form.append('task_id', task)

			axios.post(`{{ route('tasks.upload') }}`, form, {
				headers: {
					"Content-Type": "multipart/form-data"
				}
			})
			.then(res => {
				if(res.data.success) {
					Swal.fire({
						icon: 'success',
						title: 'Éxito',
						html: res.data.msg
					})
					$("#fileTable").DataTable().clear().rows.add(res.data.data).draw(false)
				} else {
					let msg = '';
					for(val of res.data.msg) {
						msg += `<p>${val}</p>`
					}
					Swal.fire({
						icon: 'error',
						title: 'Oops',
						html: msg
					})
				}
			})
			.catch(err => console.log(err))
		}
		
		function deleteFile(id, task) {
			Swal.fire({
				title: 'Estas seguro que quieres eliminar?',
				text: 'Esta acción no se puede revertir',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Eliminalo',
				cancelButtonText: 'Cancelar',
			})
			.then((result) => {
				if (result.isConfirmed) {
					axios.post(`{{ route('tasks.file.delete') }}`, { id: id, task_id: task })
					.then(res => {
						Swal.fire({
							icon: 'success',
							text: res.data.msg,
							title: 'Éxito'
						})
						$("#fileTable").DataTable().clear().rows.add(res.data.data).draw(false)
					})
					.catch(err => console.log(err))
				}
			})
		}
	</script>
@endsection