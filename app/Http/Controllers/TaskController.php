<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller {

    /**
     * Muestra la vista de las tareas.
     */
    public function index() {
        return view('tasks.index');
    }

    /**
     * Devuelve una arreglo con las tareas creadas por el usuario en sesion
     * 
     * @return mixed
     */
    public function list() {
        $data = Task::where('user_id', Auth()->user()->id)->with(['user','files'])->get();
        return response()->json(['data' => $data], Response::HTTP_OK);
    }

    /**
     * Crea o actualiza un objeto Tarea de acuerdo con la solicitud
     * 
     * @param Request
     * @return mixed
     */
    public function create(Request $request) {
        if (!isset($request['status'])) {
            $request['status'] = 1;
        }

        // Verificamos si la solicitud viene con la propieda ID, en ese caso es una actualizacion
        // de lo contrario es una nueva creacion
        if (empty($request->id)) {
            unset($request['id']);
            // Creamos la validacion para la solicitud
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ],[
                'name.required' => 'El campo Nombre es requerido',
                'description.required' => 'El campo Descripción es requerido',
                'start_date.required' => 'El campo Fecha de Inicio es requerido',
                'end_date.required' => 'El campo Fecha de Fin es requerido',
                'start_date.date' => 'El valor de Fecha de Inicio no es válido',
                'end_date.date' => 'El valor de Fecha de Fin no es válido'
            ]);

            // Verificamos si la validación paso
            if (!$validator->passes()) {
                return response()->json([
                    'success' => FALSE, 'msg' => $validator->errors()->all()
                ], Response::HTTP_OK);
            }

            // Creamos el elemento
            $task = Task::create($request->all());
            return response()->json([
                'success' => TRUE,
                'msg' => 'La tarea ha sido creada exitosamente.'
            ]);
        } else {
            $id = $request->id;
            unset($request['id']);
            // Creamos la validacion para la solicitud
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ], [
                'name.required' => 'El campo Nombre es requerido',
                'description.required' => 'El campo Descripción es requerido',
                'start_date.required' => 'El campo Fecha de Inicio es requerido',
                'end_date.required' => 'El campo Fecha de Fin es requerido',
                'start_date.date' => 'El valor de Fecha de Inicio no es válido',
                'end_date.date' => 'El valor de Fecha de Fin no es válido'
            ]);

            // Verificamos si la validacion paso
            if (!$validator->passes()) {
                return response()->json([
                    'success' => FALSE, 'msg' => $validator->errors()->all()
                ], Response::HTTP_OK);
            }

            // Obtenemos el objeto de la base de datos
            $task = Task::find($id);
            // Insertamos los cambios que se vayan hacer
            $task->fill($request->all());
            // Salvamos los cambios realizados
            $task->save();

            return response()->json([
                'success' => TRUE,
                'msg' => 'La tarea ha sido editado exitosamente.'
            ]);
        }
    }

    /**
     * Actualizamos el estado de la tarea a cerrado
     * 
     * @param Request
     * @return mixed
     */
    public function update(Request $request) {
        $task = Task::find($request->id);
        $task->fill(['status' => 0]);
        $task->save();

        return response()->json(['success' => TRUE, 'msg' => 'Se ha cerrado exitosamente el archivo.'], Response::HTTP_OK);
    }

    /**
     * La carga de archivo que va relacionada con las tareas
     * 
     * @param Request
     * @return mixed
     */
    public function uploadFile(Request $request) {
        // Creamos la validacion para el formato del archivo
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf,jpg|max:5120',
        ], [
            'file.mimes' => 'El archivo debe ser extension JPG ó PDF',
        ]);

        // Verificamos si la validacion paso
        if (!$validator->passes()) {
            return response()->json([
                'success' => FALSE,
                'msg' => $validator->errors()->all()
            ], Response::HTTP_OK);
        }

        
        $file = $request->file('file');
        // definimos el nombre del archivo
        $fileName = time().'_'.$file->getClientOriginalName();
        // Lo guardamos en la ruta de almacenamiento con una carpeta llamada 'uploads'
        $path = $file->storePubliclyAs('uploads', $fileName);

        // Creamos el objeto para relacionarlo en base de datos con las tareas
        $fileObj = TaskFile::create([
            'task_id' => $request->task_id,
            'path' => $path,
            'filename' => $fileName
        ]);

        return response()->json([
            'success' => TRUE,
            'msg' => 'Se ha cargado el archivo correctamente.',
            'data' => TaskFile::where('task_id', $request->task_id)->get()
        ], Response::HTTP_OK);
    }

    /**
     * Eliminacion del archivo fisico y de la relacion en la base de datos
     * 
     * @param Request
     * @return mixed
     */
    public function deleteFile(Request $request) {
        $file = TaskFile::find($request->id);
        Storage::delete('uploads/'.$file->filename);
        $file->delete();
        $files = TaskFile::where('task_id', $request->task_id)->get();
        return response()->json(['success' => TRUE, 'msg' => 'Se ha eliminado exitosamente el archivo.', 'data' => $files], Response::HTTP_OK);
        
    }
}