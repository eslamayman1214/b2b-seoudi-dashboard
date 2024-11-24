<x-layout>
    @section('title', 'Select Rejection Reason')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-2xl font-bold mb-6">Select Rejection Reason</h1>

            <form id="rejectionForm" action="{{ route('customers.saveRejectionReason') }}" method="POST">
                @csrf
                <input type="hidden" name="customerId" value="{{ $customerId }}">
                <input type="hidden" name="customerEmail" value="{{ request('customerEmail') }}">

                <label for="rejectedReasonId" class="block text-lg font-semibold mb-2">Rejection Reason:</label>
                <select name="rejectedReasonId" id="rejectedReasonId" class="mb-4 p-2 rounded-md border border-gray-300"
                    onchange="checkReason()">
                    <option value="">Select a reason</option>
                    @foreach ($rejectionReasons as $reason)
                        <option value="{{ $reason['value'] }}"
                            {{ old('rejectedReasonId') == $reason['value'] ? 'selected' : '' }}>
                            {{ $reason['label'] }}
                        </option>
                    @endforeach
                </select>

                <label for="note" class="block text-lg font-semibold mb-2">Note:</label>
                <textarea name="note" id="note" rows="4" class="w-full p-2 rounded-md border border-gray-300"
                    placeholder="Enter reason... (mandatory if 'Other' selected)" oninput="checkReason()"></textarea>

                <!-- Save Button -->
                <button type="button" id="saveButton" onclick="submitForm()"
                    class="bg-blue-500 text-white px-5 py-2 mt-4 rounded-lg disabled:opacity-50" disabled>
                    Save
                </button>

                <!-- Back Button -->
                <button type="button" onclick="goBack()" class="bg-gray-500 text-white px-5 py-2 mt-4 ml-4 rounded-lg">
                    Back
                </button>
            </form>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                checkReason(); // Initialize Save button state on page load
            });

            function checkReason() {
                const reasonSelect = document.getElementById('rejectedReasonId');
                const noteInput = document.getElementById('note');
                const saveButton = document.getElementById('saveButton');

                // Enable save button if reason is not "Other" or if "Other" is selected and note is filled
                if (reasonSelect.value === '249' && noteInput.value.trim() === '') {
                    saveButton.disabled = true;
                } else if (reasonSelect.value) {
                    saveButton.disabled = false;
                } else {
                    saveButton.disabled = true;
                }
            }

            async function submitForm() {
                const form = document.getElementById('rejectionForm');
                const formData = new FormData(form);

                // Basic fetch without error handling
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    // Proceed with checking response status to display appropriate alert
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Rejection reason added successfully.',
                            timer: 2000, // 2 seconds timer
                            showConfirmButton: false
                        }).then(() => {
                            // Redirect after the Swal message
                            goBack();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred. Status Code: ' + response.status
                        });
                    }
                } catch (error) {
                    alert("Error encountered in fetch request."); // Step 4: Check if an error was caught
                    console.error("Error during fetch:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Network Error',
                        text: 'Unable to process your request. Please check your connection and try again.'
                    });
                }
            }

            function goBack() {
                window.history.back();
            }
        </script>
    @endsection
</x-layout>
