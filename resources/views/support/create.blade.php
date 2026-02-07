<x-app-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-zinc-900 overflow-hidden shadow-xl sm:rounded-lg border border-zinc-800">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-white mb-6">Create Support Ticket</h2>

                    <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        {{-- Subject --}}
                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-zinc-400">Subject</label>
                            <input type="text" name="subject" id="subject" class="mt-1 block w-full rounded-md bg-zinc-800 border-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder-zinc-500" placeholder="Briefly describe your issue" required>
                            @error('subject')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Priority --}}
                        <div class="mb-4">
                            <label for="priority" class="block text-sm font-medium text-zinc-400">Priority</label>
                            <select name="priority" id="priority" class="mt-1 block w-full rounded-md bg-zinc-800 border-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Message --}}
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-zinc-400">Message</label>
                            <textarea name="message" id="message" rows="5" class="mt-1 block w-full rounded-md bg-zinc-800 border-zinc-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder-zinc-500" placeholder="Explain your issue in detail..." required></textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Attachment --}}
                        <div class="mb-6">
                            <label for="attachment" class="block text-sm font-medium text-zinc-400">Attachment (Optional)</label>
                            <input type="file" name="attachment" id="attachment" class="mt-1 block w-full text-sm text-zinc-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-zinc-800 file:text-indigo-400
                                hover:file:bg-zinc-700
                            ">
                            @error('attachment')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('support.index') }}" class="px-4 py-2 bg-zinc-800 border border-zinc-700 rounded-md font-semibold text-xs text-zinc-300 uppercase tracking-widest shadow-sm hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-zinc-800 transition ease-in-out duration-150">
                                Create Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
