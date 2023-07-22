<?php

namespace App\Http\Controllers;

use App\Models\Notificacao;
use Illuminate\Http\Request;

class NotificacaoController extends Controller
{
  
    public function cadastroNotficacao(Request $request)
    {
        $request->validate([
            'mensagem' => 'required|string|max:255',
            'tipo' => 'required',
        ]);
    
        $user = Notificacao::create([
            'mensagem' => $request->mensagem,
            'data' => now(),
            'tipo' => $request->tipo,
        ]);
        
        return redirect()->route('list');
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $notificacao = Notificacao::find($id);
    
        if ($user->profile === 'admin' || $notificacao->tipo === $user->id) {
            $notificacao->delete();
            return redirect()->back()->with('success', 'Notificação excluída com sucesso!');
        }
    
        return redirect()->back();
    }
    
}
