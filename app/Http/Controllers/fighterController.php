<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Fighter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class fighterController extends Controller
{
    public function index()
    {
        $fighters = fighter::all();

        if($fighters->isEmpty()){
           $data =[
                'message' => 'No se encontraron peleadores',
                'status' => 200
           ];
           return response()->json($data,404);
        }

        $data =[
            'fighters' => $fighters,
            'status' => 200
        ];

        return response()->json($fighters, 200);
    }


    public function store(Request $request)
    {
        
    $validator = Validator::make($request->all(), [
        'nombre' => 'required',
        'historia' => 'required',
        'estilo' => 'required',
        'icono' => 'required|max:10000|mimes:jpeg,png,jpg,gif,webp',
        'img' => 'required|max:10000|mimes:jpeg,png,jpg,gif,webp'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Error en la validación de los datos',
            'errors' => $validator->errors(),
            'status' => 400
        ], 400);
    }

    $fileIcon = $request->file('icono');
    $fileImg = $request->file('img');

    $dirIcon = 'img/icons/';
    $dirImg = 'img/fighters/';

    $filenameIcon = time() . '-' . str_replace(' ','_', $fileIcon->getClientOriginalName());
    $filenameImg = time() . '-' . str_replace(' ','_', $fileImg->getClientOriginalName());

    $uploadSucessIcon = $request->file('icono')->move($dirIcon, $filenameIcon);
    $uploadSucessImg = $request->file('img')->move($dirImg, $filenameImg);

    $pathIcon = $dirIcon . $filenameIcon;
    $pathImg = $dirImg . $filenameImg;


    // Construir URLs públicas para la respuesta
    $icono_url = asset($pathIcon);
    $img_url = asset($pathImg);


    // Crear el registro en la base de datos
    $fighter = Fighter::create([
        'nombre' => $request->nombre,
        'historia' => $request->historia,
        'estilo' => $request->estilo,
        'icono' => $icono_url,
        'img' => $img_url
    ]);

    if (!$fighter) {
        return response()->json([
            'message' => 'Error al crear el peleador',
            'status' => 500
        ], 500);
    }


    return response()->json([
        'peleador' => $fighter,
        'status' => 201
    ], 201);
    }

    public function show($id)
    {
        $fighter = fighter::find($id);

        if(!$fighter){
            $data = [
                'message' => 'Peleador no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $data = [
            'fighter' => $fighter,
            'status' => 200
        ];

        return response()->json($data, 200);

    }

    public function destroy($id)
    {   
        $fighter = fighter::find($id);

        if(!$fighter){
            $data = [
                'message' => 'Peleador no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $fighter-> delete();

        $data =[
            'message'=> 'Peleador eliminado',
            'status' => 200
        ];

        return response()-> json($data, 200);

    }

    public function update(Request $request, $id)
    {
       

    $fighter = Fighter::find($id);

    if (!$fighter) {
        return response()->json([
            'message' => 'Peleador no encontrado',
            'status' => 404
        ], 404);
    }

    $validator = Validator::make($request->all(), [
        'nombre' => 'required',
        'historia' => 'required',
        'estilo' => 'required',
        'icono' => 'nullable|max:2048|mimes:jpeg,png,jpg,gif,webp',
        'img' => 'nullable|max:2048|mimes:jpeg,png,jpg,gif,webp'
    ]);

    if ($validator->fails()) {
        $data = [
            'message' => 'Error en la validacion de los datos',
            'errors' => $validator->errors(),
            'status' => 400
        ];
        return response()->json($data,400);
    }

    // Actualizar campos básicos
    $fighter->nombre = $request->nombre;
    $fighter->historia = $request->historia;
    $fighter->estilo = $request->estilo;

    // Manejo de la imagen "icono"
    if ($request->hasFile('icono')) {
        // Eliminar la imagen anterior si existe
        if ($fighter->icono) {
            $oldIconPath = str_replace(asset(''), '', $fighter->icono);
            if (file_exists(public_path($oldIconPath))) {
                unlink(public_path($oldIconPath));
            }
        }

        // Subir la nueva imagen
        $fileIcon = $request->file('icono');
        $dirIcon = 'img/fighters/';
        $filenameIcon = time() . '-' . str_replace(' ','_', $fileIcon->getClientOriginalName());
        $pathIcon = $fileIcon->move($dirIcon, $filenameIcon);
        $fighter->icono = asset($dirIcon . $filenameIcon);
    }

    // Manejo de la imagen "img"
    if ($request->hasFile('img')) {
        // Eliminar la imagen anterior si existe
        if ($fighter->img) {
            $oldImgPath = str_replace(asset(''), '', $fighter->img);
            if (file_exists(public_path($oldImgPath))) {
                unlink(public_path($oldImgPath));
            }
        }

        // Subir la nueva imagen
        $fileImg = $request->file('img');
        $dirImg = 'img/icons/';
        $filenameImg = time() . '-' . str_replace(' ','_', $fileImg->getClientOriginalName());
        $pathImg = $fileImg->move($dirImg, $filenameImg);
        $fighter->img = asset($dirImg . $filenameImg);
    }

    // Guardar cambios
    $fighter->save();

    return response()->json([
        'message' => 'Peleador actualizado con éxito',
        'fighter' => $fighter,
        'status' => 200
    ], 200);
    }



    public function updatePartial(Request $request, $id)
    {
        $fighter = fighter::find($id);

        if(!$fighter){
            $data = [
                'message' => 'Peleador no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => '',
            'historia' => '',
            'estilo' => '',
            'icono' => '',
            'img' => ''

        ]);

        if($validator->fails()){
            $data=[
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        if($request->has('nombre')){
            $fighter -> nombre = $request->nombre;
        }

        if($request->has('historia')){
            $fighter -> historia = $request->historia;
        }

        if($request->has('estilo')){
            $fighter -> estilo = $request->estilo;
        }

        if($request->has('icono')){
            $fighter -> icono = $request->icono;
        }

        if($request->has('imagen')){
            $fighter -> imagen = $request->imagen;
        }

        $fighter->save();

        $data = [
            'message' => 'Peleador actualizado',
            'fighter' => $fighter,
            'status' => 200
        ];

        return response()->json($data, 200);



    }

}
