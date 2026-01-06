<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm rounded-lg">

                <h1 class="text-xl font-bold mb-4">Cadastrar Bar</h1>

                <form method="POST" action="/bar">
                    @csrf

                    <div class="mb-4">
                        <label class="block">CNPJ</label>
                        <input
                            type="text"
                            name="cnpj"
                            class="border rounded w-full p-2"
                            required
                        >
                    </div>


                    <div class="mb-4">
                        <label class="block">Nome do Bar</label>
                        <input
                            type="text"
                            name="name"
                            class="border rounded w-full p-2"
                        >
                    </div>

                    <button
                        type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded"
                    >
                        Salvar
                    </button>
                </form>

                @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif


            </div>
        </div>
    </div>
</x-app-layout>
