<x-guest-layout>
    <div class="w-full min-h-screen py-6 container mx-auto">
        @foreach ($categories as $category)
            <div class="max-w-xs mx-4 mb-2 rounded-lg shadow-lg">
                <img class="w-full h-48" src="{{ Storage::url($category->image) }}" alt="Image" />
                <a href="{{ route('categories.show', ['category' => $category->id]) }}">
                    <div class="px-6 py-4">
                        <h4
                            class="mb-3 text-xl font-semibold tracking-tight text-green-600 uppercase hover:text-green-400">
                            {{ $category->name }}
                        </h4>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</x-guest-layout>
