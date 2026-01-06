<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bar;

class BarController extends Controller
{
    //exibe o formulário de cadastro do bar
    //rota bar/create
    public function create()
    {
        //retorna a view onde o dono do bar cadastra o bar
        return view('bar.create');
    }

    //processa o envio do formulário de cadastro do bar
    //rota POST
    public function store(Request $request)
    {
        //Recupera o usuário atualmente autenticado
        $user = auth()->user();

        //regra: um usuário só pode ter um único bar cadastrado (CNPJ)
        if ($user->bar) {
            //se ja tem bar, retorna
            return back()->with('error', 'Você já possui um bar cadastrado.');
        }

        if (Bar::where('cnpj', $request->cnpj)->exists()) {
            //não deixa duplicar cnpj
            return back()->with('error', 'Este CNPJ já está cadastrado.');
        }

        //bar criado no banco de dados
        Bar::create([
            //relacionamento usuario = bar
            'user_id' => $user->id,
            'name' => $request->name,
            'cnpj' => $request->cnpj,
        ]);

        //após realizar o cadastro, redireciona para a dashboard
        return redirect('/dashboard')
            ->with('success', 'Bar cadastrado com sucesso!');
    }

    //pronto para cadastro de eventos
}
