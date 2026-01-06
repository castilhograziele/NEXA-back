<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function create()
    {
        // pega o usuário logado
        $user = auth()->user();

        // verifica se o usuário possui um bar
        $bar = $user->bar;

        // se não tiver bar, bloqueia o acesso ao formulário
        if (!$bar) {
            return redirect('/bar/create')
                ->with('error', 'Cadastre um bar antes de criar eventos.');
        }

        // se o bar existir, mostra o formulário de criação do evento
        return view('events.create');
    }

    public function store(Request $request)
    {
        // pega o usuário logado
        $user = auth()->user();

        // recupera o bar vinculado ao usuário
        $bar = $user->bar;

        // proteção extra: impede salvar evento sem bar
        if (!$bar) {
            return redirect('/bar/create')
                ->with('error', 'Você precisa cadastrar um bar antes de criar eventos.');
        }

        // cria o evento já vinculado ao bar correto
        Event::create([
            'bar_id' => $bar->id,
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'category' => $request->category,
        ]);

        // após salvar, retorna para a dashboard
        return redirect('/dashboard')
            ->with('success', 'Evento criado com sucesso!');
    }
}
