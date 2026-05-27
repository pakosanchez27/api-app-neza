<?php

namespace App\Http\Controllers;

use App\Mail\PreregistroApprovedMail;
use App\Mail\PreregistroCorrectionMail;
use App\Mail\PreregistroRejectedMail;
use App\Models\Preregistro;
use App\Models\User;
use App\Services\ApprovedPreregisterIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use RuntimeException;

class AprobarComerciosController extends Controller
{
    public function __construct(private readonly ApprovedPreregisterIntegrationService $integrationService)
    {
    }

    public function index()
    {
        $comercios = Preregistro::query()
            ->with('tipoRelacion:id_tipo,nombre')
            ->orderBy('estatus_registro')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.aprobar-comercios.index', compact('comercios'));
    }

    public function approve(Preregistro $preregistro)
    {
        if (User::query()->where('email', $preregistro->correo)->exists()) {
            return redirect()
                ->route('admin.aprobar-comercios')
                ->with('error', 'Ya existe un usuario con ese correo. No fue posible aprobar el preregistro.');
        }

        $temporaryPassword = Str::slug(Str::random(8));

        try {
            DB::transaction(function () use ($preregistro, $temporaryPassword) {
                $this->integrationService->createApprovedCommerce(
                    $this->buildApprovedPayload($preregistro),
                    false,
                    $temporaryPassword
                );

                $preregistro->update([
                    'estatus_registro' => Preregistro::ESTATUS_ACEPTADO,
                    'observacion_registro' => null,
                    'token_correccion' => null,
                    'token_correccion_expira_en' => null,
                ]);
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.aprobar-comercios')
                ->with('error', $exception->getMessage());
        }

        try {
            if (! empty($preregistro->correo)) {
                Mail::to($preregistro->correo)->send(new PreregistroApprovedMail(
                    $preregistro->fresh(),
                    $temporaryPassword,
                    'https://exploraneza.digitalneza.com/auth/comercios/login'
                ));
            }
        } catch (\Throwable $exception) {
            report($exception);

            Log::warning('No fue posible enviar el correo de aprobacion del preregistro.', [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'correo' => $preregistro->correo,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.aprobar-comercios')
            ->with('success', 'Comercio aprobado, integrado al sistema y correo enviado correctamente.');
    }

    public function requestCorrection(Request $request, Preregistro $preregistro)
    {
        $validated = $request->validate([
            'observacion_registro' => 'required|string|max:1000',
        ]);

        $preregistro->update([
            'estatus_registro' => Preregistro::ESTATUS_REQUIERE_CORRECCION,
            'observacion_registro' => $validated['observacion_registro'],
            'token_correccion' => Str::random(80),
            'token_correccion_expira_en' => now()->addDays(7),
        ]);

        try {
            if (! empty($preregistro->correo)) {
                Mail::to($preregistro->correo)->send(new PreregistroCorrectionMail(
                    $preregistro,
                    $this->buildCorrectionUrl($preregistro->token_correccion)
                ));
            }
        } catch (\Throwable $exception) {
            report($exception);

            Log::warning('No fue posible enviar el correo de correccion del preregistro.', [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'correo' => $preregistro->correo,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.aprobar-comercios')
            ->with('success', 'Solicitud de correccion guardada y correo enviado correctamente.');
    }

    public function reject(Request $request, Preregistro $preregistro)
    {
        $validated = $request->validate([
            'observacion_registro' => 'required|string|max:1000',
        ]);

        $preregistro->update([
            'estatus_registro' => Preregistro::ESTATUS_RECHAZADO_DEFINITIVO,
            'observacion_registro' => $validated['observacion_registro'],
            'token_correccion' => null,
            'token_correccion_expira_en' => null,
        ]);

        try {
            if (! empty($preregistro->correo)) {
                Mail::to($preregistro->correo)->send(new PreregistroRejectedMail($preregistro));
            }
        } catch (\Throwable $exception) {
            report($exception);

            Log::warning('No fue posible enviar el correo de rechazo del preregistro.', [
                'id_preresgistro' => $preregistro->id_preresgistro,
                'correo' => $preregistro->correo,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.aprobar-comercios')
            ->with('success', 'Rechazo definitivo guardado y correo enviado correctamente.');
    }

    private function buildCorrectionUrl(string $token): string
    {
        $frontendUrl = rtrim((string) env('FRONTEND_URL', config('app.url')), '/');

        return $frontendUrl . '/auth/comercios/registro/correccion/' . $token;
    }

    private function buildApprovedPayload(Preregistro $preregistro): array
    {
        return [
            'solicitante' => [
                'nombre' => $preregistro->nombre_p,
                'apellido_p' => $preregistro->app_p,
                'apellido_m' => $preregistro->apm_p,
                'email' => $preregistro->correo,
                'telefono' => $preregistro->telefono,
            ],
            'establecimiento' => [
                'nombre_comercial' => $preregistro->nombre_est,
                'razon_social' => $preregistro->razon_social,
                'tipo_id' => $preregistro->tipo,
                'descripcion' => $preregistro->descripcion_est,
            ],
            'ubicacion' => [
                'calle' => $preregistro->calle,
                'colonia' => $preregistro->colonia,
                'num_ext' => $preregistro->numero,
                'cp' => $preregistro->codigo_postal,
                'latitud' => $preregistro->latitud,
                'longitud' => $preregistro->longitud,
            ],
            'documentos' => [
                'ine' => $preregistro->ine,
                'licencia_funcionamiento' => $preregistro->lic_fun,
                'foto_establecimiento' => $preregistro->foto_est,
            ],
        ];
    }
}
