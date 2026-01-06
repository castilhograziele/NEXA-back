<x-app-layout>
    <h1>Criar Evento</h1>

    <form method="POST" action="/events">
        @csrf

        <input type="text" name="title" placeholder="Título do evento"><br>
        <input type="date" name="event_date"><br>
        <input type="time" name="event_time"><br>
        <input type="text" name="category" placeholder="Categoria"><br>

        <textarea name="description" placeholder="Descrição"></textarea><br>

        <button type="submit">Salvar</button>
    </form>
</x-app-layout>
