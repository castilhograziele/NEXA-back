<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // pega o usuário logado
        $user = auth()->user();

        // se o usuário ainda não tem bar cadastrado
        if (!$user->bar) {
            // redireciona para a tela de cadastro do bar
            return redirect('/bar/create');
        }

        // recupera o bar do usuário
        $bar = $user->bar;

        // busca todos os eventos vinculados a esse bar
        $events = $bar->events;

        // envia os dados do bar e eventos para a view da dashboard
        return view('dashboard', compact('bar', 'events'));
    }
}
