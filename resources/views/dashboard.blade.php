<h1>Dashboard do Bar</h1>

<p><strong>Nome:</strong> {{ $bar->name }}</p>
<p><strong>CNPJ:</strong> {{ $bar->cnpj }}</p>

<hr>

<a href="/events/create">Criar novo evento</a>

<hr>

<h2>Eventos</h2>

@forelse ($events as $event)
    <div style="margin-bottom: 10px;">
        <strong>{{ $event->title }}</strong><br>
        {{ $event->event_date }} às {{ $event->event_time }}<br>
        Categoria: {{ $event->category }}
    </div>
@empty
    <p>Nenhum evento cadastrado ainda.</p>
@endforelse
