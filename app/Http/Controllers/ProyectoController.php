<?php

namespace App\Http\Controllers;

use App\Integrante;
use App\Proyecto;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;

class ProyectoController extends Controller
{
    private function jsonResponse($code, $params)
    {
        $jsonResponse = response()->json($params);
        $jsonResponse->setStatusCode($code);
        return $jsonResponse;
    }

    private function createNewProjectValidator(Request $request)
    {
        $rules = [
            'anteproyecto' => ['required', 'mimes:pdf'],
            'txtNombreProyecto' => ['required', 'unique:proyecto,titulo'],
            'txtDescripcion' => ['required'],
            'txtImpactoSocial' => ['required'],
            'txtFactibilidad' => ['required'],
            'txtCronograma' => ['required'],
            'txtMetodologia' => ['required'],
            'txtResultados' => ['required'],
            'txtPlanNegocios' => ['required'],
            'selIntegrantes' => ['required']
        ];

        $messages = [
            'anteproyecto.required' => 'El anteproyecto es requerido',
            'anteproyecto.mimes' => 'El anteproyecto debe ser un archivo pdf',
            'selIntegrantes.required' => 'El número de integrantes del proyecto es requerido',
            'txtNombreProyecto.required' => 'El nombre del proyecto es requerido',
            'txtNombreProyecto.unique' => 'El título del proyecto ya existe',
            'txtDescripcion.required' => 'La descripción del proyecto es requerida',
            'txtImpactoSocial.required' => 'El impacto social del proyecto es requerido',
            'txtFactibilidad.required' => 'El análisis de factibilidad del proyecto es requerido',
            'txtCronograma.required' => 'El cronograma de actividades del proyecto es requerido',
            'txtMetodologia.required' => 'La metodología del proyecto es requerido',
            'txtResultados.required' => 'Los resultados esperados del proyecto son requeridos',
            'txtPlanNegocios.required' => 'El plan de negocios del proyecto es requerido',
        ];

        $numIntegrantes = $request['selIntegrantes'];

        if ($numIntegrantes) {
            for ($i = 1; $i <= $numIntegrantes; $i++) {
                $rules['nombreIntegrante' . $i] = 'required';
                $messages['nombreIntegrante' . $i . '.required'] =
                    'El nombre del integrante ' . $i . ' es requerido';
                $rules['priApellido' . $i] = 'required';
                $messages['priApellido' . $i . '.required'] =
                    'El primer apellido del integrante ' . $i . ' es requerido';
                $rules['fechaNacimiento' . $i] = 'required';
                $messages['fechaNacimiento' . $i . '.required'] =
                    'La fecha de nacimiento del integrante ' . $i .
                    ' es requerida';
                $rules['nivelEstudios' . $i] = 'required';
                $messages['nivelEstudios' . $i . '.required'] =
                    'El nivel de estudios del integrante ' . $i .
                    ' es requerido';
                $rules['email' . $i] = ['required', 'email', 'unique:integrante,email'];
                $messages['email' . $i . '.required'] =
                    'El email del integrante ' . $i . ' es requerido';
                $messages['email' . $i . '.email'] =
                    'El email del integrante ' . $i .
                    ' tiene un formato incorrecto';
                $messages['email' . $i . '.unique'] =
                    'El email del integrante ' . $i . ' ya está siendo usado';
                $rules['carrera' . $i] = 'required';
                $messages['carrera' . $i . '.required'] =
                    'La carrera del integrante ' . $i . ' es requerida';
                $rules['carrera' . $i] = 'required';
                $messages['carrera' . $i . '.required'] =
                    'La carrera del integrante ' . $i . ' es requerida';
                $rules['universidad' . $i] = 'required';
                $messages['universidad' . $i . '.required'] =
                    'La universidad del integrante ' . $i . ' es requerida';
                $rules['constanciaEstudios' . $i] = 'required';
                $messages['constanciaEstudios' . $i . '.required'] =
                    'La constancia de estudios del integrante ' . $i .
                    ' es requerida';
                $rules['constanciaObligaciones' . $i] = 'required';
                $messages['constanciaObligaciones' . $i . '.required'] =
                    'La constancia de obligaciones del integrante ' . $i .
                    ' es requerida';
                $rules['ine' . $i] = 'required';
                $messages['ine' . $i . '.required'] =
                    'La INE del integrante ' . $i . ' es requerida';
                $rules['curp' . $i] = 'required';
                $messages['curp' . $i . '.required'] =
                    'La CURP del integrante ' . $i . ' es requerida';
                $rules['oficioProtesta' . $i] = 'required';
                $messages['oficioProtesta' . $i . '.required'] =
                    'El oficio bajo protesta de decir verdad del integrante ' .
                    $i . ' es requerido';
            }
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        return $validator;
    }

    public function registrarProyecto(Request $request)
    {
        $validator = $this->createNewProjectValidator($request);

        if ($validator->fails()) {
            return $this->jsonResponse(400, ['errors' => $validator->errors()
                ->all()]);
        }

        try {
            $this->createProyecto($request);

            for ($i = 1; $i <= $request['selIntegrantes']; $i++) {
                $this->createIntegrante($request, $i);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(400, ['errors' => var_dump($e)]);
        }

        return $this->jsonResponse(200, $request->all());
    }

    private function createProyecto(Request $request)
    {
        $proyecto = new Proyecto();
        $proyecto->titulo = $request['txtNombreProyecto'];
        $proyecto->descripcion = $request['txtDescripcion'];
        $proyecto->impacto = $request['txtImpactoSocial'];
        $proyecto->factibilidad = $request['txtFactibilidad'];
        $proyecto->cronograma = $request['txtCronograma'];
        $proyecto->metodologia = $request['txtMetodologia'];
        $proyecto->resultados = $request['txtResultados'];
        $proyecto->plan_negocios = $request['txtPlanNegocios'];

        $proyecto->save();

        $anteproyecto = $request->file('anteproyecto');
        $anteproyectoName = 'anteproyecto_' . $proyecto->id . '.pdf';

        $proyecto->anteproyecto = $anteproyectoName;

        $proyecto->save();

        $this->saveFile($anteproyecto, 'solicitudes', $anteproyectoName);
    }

    private function createIntegrante(Request $request, $numIntegrante)
    {
        $integrante = new Integrante();
        $integrante->nombre = $request['nombreIntegrante' . $numIntegrante];
        $integrante->pri_apellido = $request['priApellido' . $numIntegrante];
        $integrante->seg_apellido = $request['segApellido' . $numIntegrante];
        $integrante->email = $request['email' . $numIntegrante];
        $integrante->nacimiento = $request['fechaNacimiento' . $numIntegrante];
        $integrante->nivel_estudio = $request['nivelEstudios' . $numIntegrante];
        $integrante->carrera = $request['carrera' . $numIntegrante];
        $integrante->universidad = $request['universidad' . $numIntegrante];

        $integrante->save();

        $titulo = $request->file('titulo' . $numIntegrante);
        $tituloName = 'titulo_' . $integrante->id . '.pdf';
        $constanciaEstudios = $request->file('constanciaEstudios' .
            $numIntegrante);
        $constanciaEstudiosName = 'constanciaEstudios_' . $integrante->id .
            '.pdf';
        $constanciaObligaciones = $request->file('constanciaObligaciones' .
            $numIntegrante);
        $constanciaObligacionesName = 'constanciaObligaciones_' .
            $integrante->id . '.pdf';
        $ine = $request->file('ine' . $numIntegrante);
        $ineName = 'ine_' . $integrante->id . '.pdf';
        $curp = $request->file('curp' . $numIntegrante);
        $curpName = 'curp_' . $integrante->id . '.pdf';
        $oficioProtesta = $request->file('oficioProtesta' . $numIntegrante);
        $oficioProtestaName = 'oficioProtesta_' . $integrante->id . '.pdf';
        $this->saveFile($titulo, 'solicitudes', $tituloName);
        $this->saveFile($constanciaEstudios, 'solicitudes',
            $constanciaEstudiosName);
        $this->saveFile($constanciaObligaciones, 'solicitudes',
            $constanciaObligacionesName);
        $this->saveFile($ine, 'solicitudes', $ineName);
        $this->saveFile($curp, 'solicitudes', $curpName);
        $this->saveFile($oficioProtesta, 'solicitudes', $oficioProtestaName);

        $integrante->titulo_profesional = $tituloName;
        $integrante->constancia_estudios = $constanciaEstudiosName;
        $integrante->constancia_obligaciones = $constanciaObligacionesName;
        $integrante->ine = $ineName;
        $integrante->curp = $curpName;
        $integrante->protesta_verdad = $oficioProtestaName;

        $integrante->save();
    }

    private function saveFile($requestFile, $folder, $newName)
    {
        $requestFile->move(
            base_path() . '/public/' . $folder, $newName
        );
    }
}
