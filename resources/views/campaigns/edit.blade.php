<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Campaign') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('campaigns.update', $campaign) }}">
                        @csrf
                        @method('PUT')

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block font-medium text-sm text-gray-700">Subject</label>
                            <input id="subject"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="text" name="subject" value="{{ old('subject', $campaign->subject) }}" required
                                autofocus />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        <!-- Sender Selection -->
                        <div class="mt-4">
                            <label for="sender_id" class="block font-medium text-sm text-gray-700">From Identity</label>
                            <select id="sender_id" name="sender_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Default (System Settings)</option>
                                @foreach($senders as $sender)
                                    <option value="{{ $sender->id }}" {{ (old('sender_id', $campaign->sender_id) == $sender->id) ? 'selected' : '' }}>
                                        {{ $sender->name }} &lt;{{ $sender->email }}&gt;
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('sender_id')" class="mt-2" />
                        </div>

                        <!-- Body -->
                        <div class="mt-4">
                            <label for="body" class="block font-medium text-sm text-gray-700">Body</label>
                            <textarea id="body" name="body" rows="6"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required>{{ old('body', $campaign->body) }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </div>

                        <!-- Scheduled At -->
                        <div class="mt-4">
                            <label for="scheduled_at" class="block font-medium text-sm text-gray-700">Schedule
                                (Optional)</label>
                            <input id="scheduled_at"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                type="datetime-local" name="scheduled_at"
                                value="{{ old('scheduled_at', $campaign->scheduled_at ? $campaign->scheduled_at->format('Y-m-d\TH:i') : '') }}" />
                            <x-input-error :messages="$errors->get('scheduled_at')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('campaigns.show', $campaign) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-2">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Update Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/lf9ppiv4jl12tfzb89rbrgqiew0sgwnuea0f28t4nmh0hof2/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#body',
            height: 400,
            menubar: false,
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed linkchecker a11ychecker tinymcespellchecker permanentpen powerpaste advtable advcode editimage tinycomments mentions tableofcontents footnotes mergetags autocorrect typography inlinecss',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
            tinycomments_mode: 'embedded',
            tinycomments_author: 'Author name',
            mergetags_list: [
                { value: 'First.Name', title: 'First Name' },
                { value: 'Email', title: 'Email' },
            ],
            ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant")),
            skin: 'oxide',
            content_css: 'default',
            setup: function (editor) {
                editor.on('change', function () {
                    tinymce.triggerSave();
                });
            }
        });
    </script>
</x-app-layout>