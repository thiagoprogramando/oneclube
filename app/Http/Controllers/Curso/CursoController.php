<?php

namespace App\Http\Controllers\Curso;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;
use App\Models\Curso;
use App\Models\Invoice;
use App\Models\Material;
use Illuminate\Http\Request;

class CursoController extends Controller {

    public function list() {

        $cursos = Curso::all();
        return view('dashboard.curso.listCurso', [
            'cursos' => $cursos
        ]);
    }

    public function createCurso(Request $request) {

        $curso = new Curso();
        $curso->id_user         = auth()->id();
        $curso->title           = $request->title;
        $curso->description     = $request->description;
        $curso->value           = $this->formatarValor($request->value);

        if($curso->save()) {
            return redirect()->back()->with('success', 'Curso cadastro com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function deleteCurso($id) {

        $curso = Curso::find($id);
        if($curso) {

            $curso->delete();
            return redirect()->back()->with('success', 'Curso excluído com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function viewCurso($id) {

        $curso = Curso::find($id);
        if($curso) {

            $user = auth()->user();

            $invoice = Invoice::where('idUser', auth()->id())->where('name', $curso->title)->count();
            if($invoice < 1 & $user->type != 1) {

                $assas = new AssasController();
                $charge = $assas->createChargeCurso(auth()->user()->customer, $curso->value, $curso->description);

                if($charge == null) {
                    return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
                }

                $invoice = new Invoice();
                $invoice->idUser        = auth()->id();
                $invoice->name          = $curso->title;
                $invoice->description   = $curso->description;
                $invoice->value         = $curso->value;
                $invoice->commission    = ($curso->value * 0.20);
                $invoice->type          = 5;
                $invoice->status        = 'PENDING_PAY';
                $invoice->token         = $charge['id'];
                $invoice->url           = $charge['invoiceUrl'];

                if($invoice->save()) {
                    return redirect()->route('invoices')->with('success', 'Agora é só finalizar o pagamento para ter acesso!');
                }
        
                return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
            }

            $materiais = Material::where('id_curso', $curso->id)->get();
            return view('dashboard.curso.viewCurso', [
                'curso'         => $curso,
                'materiais'     => $materiais
            ]);
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function createMaterial(Request $request) {

        $arquivo = $request->file('file');
        $material = new Material();
        $material->id_curso     = $request->id_curso;
        $material->title        = $request->title;
        $material->description  = $request->description;
        $material->type         = $request->type;
        $material->file         = $arquivo->store('curso');
        if($material->save()) {
            return redirect()->back()->with('success', 'Material cadastro com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    public function deleteMaterial($id) {

        $material = Material::find($id);
        if($material) {

            $material->delete();
            return redirect()->back()->with('success', 'Material excluído com Sucesso!');
        }

        return redirect()->back()->with('error', 'Houve um problema, tente novamente mais tarde!');
    }

    private function formatarValor($valor) {
        
        $valor = preg_replace('/[^0-9,.]/', '', $valor);
        $valor = str_replace(['.', ','], '', $valor);

        return number_format(floatval($valor) / 100, 2, '.', '');
    }

}
