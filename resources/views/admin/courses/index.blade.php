<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Courses') }}
            </h2>
            <a href="{{ route('admin.courses.create') }}" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                Add New
            </a>
        </div>
    </x-slot>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-10 flex flex-col gap-y-5">

            @forelse($courses as $course)
                <div class="item-card flex flex-col md:flex-row gap-y-10 justify-between md:items-center">
                    <div class="flex flex-row items-center gap-x-3">
                        <img src="{{ Storage::url($course->thumbnail) }}" alt="" class="rounded-2xl object-cover w-[120px] h-[90px]">
                        <div class="flex flex-col">
                            <h3 class="text-indigo-950 text-xl font-bold">{{ $course->name }}</h3>
                            {{-- tembak category juga karna kita ambil name di table category (category berelasi dengan course) --}}
                            <p class="text-slate-500 text-sm">{{ $course->category->name }}</p>
                        </div>
                    </div>
                    <div class="hidden md:flex flex-col">
                        <p class="text-slate-500 text-sm">Students</p>
                        {{-- hitung jumlah students --}}
                        <h3 class="text-indigo-950 text-xl font-bold">{{ $course->students->count() }}</h3>
                    </div>
                    <div class="hidden md:flex flex-col">
                        <p class="text-slate-500 text-sm">Videos</p>
                        <h3 class="text-indigo-950 text-xl font-bold">{{ $course->course_videos->count() }}</h3>
                    </div>
                    <div class="hidden md:flex flex-col">
                        <p class="text-slate-500 text-sm">Teacher</p>
                        {{-- tembah nama teacher yg ada di table user --}}
                        <h3 class="text-indigo-950 text-xl font-bold">{{ $course->teacher->user->name }}</h3>
                    </div>
                    <div class="hidden md:flex flex-row items-center gap-x-3">
                        <a href="{{ route('admin.courses.show', $course) }}" class="font-bold py-4 px-6 bg-indigo-700 text-white rounded-full">
                            Manage
                        </a>
                        {{-- kirim route destroy dan masukan variable course utk mengirim data kelas yg mau di hapus --}}
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-bold py-4 px-6 bg-red-700 text-white rounded-full">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p>
                    Belum ada kelas yang ditambahkan
                </p>
            @endforelse
                
            </div>
        </div>
    </div>
</x-app-layout>
