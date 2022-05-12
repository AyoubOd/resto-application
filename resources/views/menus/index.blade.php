<x-guest-layout>
    <div class="w-full min-h-screen py-6 container mx-auto">
        <div class="grid lg:grid-cols-4 gap-y-6">
            @foreach ($menus as $menu)
                <div class="max-w-xs mx-4 mb-2 rounded-lg shadow-lg">
                    <img class="w-full h-48" src="{{ Storage::url($menu->image) }}" alt="Image" />
                    <div class="px-6 py-4">
                        <h4
                            class="mb-3 text-xl font-semibold tracking-tight text-green-600 uppercase hover:text-green-400">
                            {{ $menu->name }}
                        </h4>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-guest-layout>
