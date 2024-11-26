<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Responsable;
use App\Models\User;
use App\Models\Escuela;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ResponsableApiController extends Controller
{
    /**
     * Display a listing of responsables.
     */
    public function index(Request $request)
    {
        $nombre = $request->input('nombre');
        $adminId = Auth::id();

        // Filtrado de escuelas por administrador
        $escuelas = Escuela::whereHas('administrador', function ($query) use ($adminId) {
            $query->where('cesi_administrador_id', $adminId);
        })->pluck('id');

        // Obtener responsables filtrados por escuela y nombre (si se proporciona)
        $responsables = Responsable::with(['tutores' => function ($query) use ($escuelas) {
                $query->whereIn('cesi_escuela_id', $escuelas);
            }])
            ->where('responsable_activacion', 1)
            ->whereHas('tutores', function ($query) use ($escuelas) {
                $query->whereIn('cesi_escuela_id', $escuelas);
            })
            ->when($nombre, function ($query, $nombre) {
                return $query->where('responsable_nombre', 'like', '%' . $nombre . '%');
            })
            ->get();

        return response()->json($responsables, 200);
    }

    /**
     * Store a newly created responsable in storage.
     */
    public function store(Request $request) // Cambiado para usar FormRequest
    {
        try {
            // Validación ya se maneja en el FormRequest
            $request->validationRules($request);
            $responsable = new Responsable();
            $responsable->fill($request->only(['responsable_nombre', 'responsable_usuario', 'responsable_telefono', 'cesi_tutore_id']));
            $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña);
            $responsable->responsable_activacion = 0;

            // Crear el usuario asociado
            $user = new User();
            $user->name = $request->responsable_nombre;
            $user->email = $request->responsable_usuario;
            $user->password = bcrypt($request->responsable_contraseña);
            $user->role = 'responsable';

            // Manejo de la foto del responsable
            if ($request->hasFile('responsable_foto')) {
                $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
                $responsable->responsable_foto = $imagePath;
            }

            $user->save();
            $responsable->save();

            return response()->json(['message' => 'Responsable creado exitosamente', 'data' => $responsable], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear responsable', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $responsable = Responsable::find($id);

        if (!$responsable) {
            return response()->json(['error' => 'Responsable no encontrado'], 404);
        }

        return response()->json(['data' => $responsable], 200);
    }

    /**
     * Update the specified responsable in storage.
     */
    public function update(Request $request, Responsable $responsable) // Cambiado para usar FormRequest
    {
        try {
            $request->validationRules($request);
            $responsable->fill($request->only(['responsable_nombre', 'responsable_usuario', 'responsable_telefono', 'cesi_tutore_id']));

            // Actualizar la contraseña solo si se ha proporcionado
            if ($request->filled('responsable_contraseña')) {
                $responsable->responsable_contraseña = bcrypt($request->responsable_contraseña);
            }

            // Manejo de la actualización de la foto
            if ($request->hasFile('responsable_foto')) {
                // Eliminar foto anterior si existe
                if ($responsable->responsable_foto) {
                    $this->deletePhoto($responsable->responsable_foto);
                }

                // Guardar nueva foto
                $imagePath = $request->file('responsable_foto')->store('responsables', 'public');
                $responsable->responsable_foto = $imagePath;
            }

            $responsable->responsable_activacion = $request->responsable_activacion;

            // Actualizar el usuario asociado
            $user = User::find('email',$responsable->responsable_usuario);
            $user->name = $request->responsable_nombre;
            $user->email = $request->responsable_usuario;
            if ($request->filled('responsable_contraseña')) {
                $user->password = bcrypt($request->responsable_contraseña);
            }
            $user->role = 'responsable';
            $user->save();

            $responsable->save();

            return response()->json(['message' => 'Responsable actualizado exitosamente', 'data' => $responsable], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar responsable', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified responsable from storage.
     */
    public function destroy(Responsable $responsable)
    {
        try {
            // Eliminar la foto del responsable
            if ($responsable->responsable_foto) {
                $this->deletePhoto($responsable->responsable_foto);
            }

            // Eliminar el usuario
            $user = User::find($responsable->cesi_responsable_id);
            $user->delete();

            $responsable->delete();

            return response()->json(['message' => 'Responsable eliminado exitosamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar responsable', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete the photo from storage.
     */
    protected function deletePhoto($photoPath)
    {
        $fullPath = public_path('storage/' . $photoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath); // Eliminar la foto del almacenamiento
        }
    }

    public function validationRules($responsableId = null)
    {
        return [
            'rules' => [
                'responsable_nombre' => 'required|string|max:255',
                'responsable_usuario' => 'required|email|unique:cesi_responsables,responsable_usuario,' . $responsableId,
                'responsable_contraseña' => 'nullable|string|min:6',
                'responsable_telefono' => 'nullable|regex:/^[0-9]{10,11}$/',
                'responsable_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ],
            'messages' => [
                'responsable_nombre.required' => 'El nombre del responsable es obligatorio.',
                'responsable_nombre.string' => 'El nombre del responsable debe ser una cadena de texto.',
                'responsable_nombre.max' => 'El nombre del responsable no puede exceder los 255 caracteres.',

                'responsable_usuario.required' => 'El correo electrónico del responsable es obligatorio.',
                'responsable_usuario.email' => 'El correo electrónico debe tener un formato válido.',
                'responsable_usuario.unique' => 'Este correo electrónico ya está registrado.',

                'responsable_contraseña.nullable' => 'La contraseña es opcional.',
                'responsable_contraseña.string' => 'La contraseña debe ser una cadena de texto.',
                'responsable_contraseña.min' => 'La contraseña debe tener al menos 6 caracteres.',

                'responsable_telefono.regex' => 'El número de teléfono debe contener entre 10 y 11 dígitos.',

                'responsable_foto.image' => 'El archivo debe ser una imagen.',
                'responsable_foto.mimes' => 'La imagen debe estar en formato jpeg, png, jpg o gif.',
                'responsable_foto.max' => 'La imagen no puede superar los 2 MB.',
            ],
        ];
    }

}
