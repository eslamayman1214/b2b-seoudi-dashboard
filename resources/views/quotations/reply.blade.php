<x-layout>
    @section('title', 'Reply to Quotation')
    @section('content')
        <div class="container mx-auto px-4 py-8">
            <div class="flex space-x-8">
                <!-- Left Half: Quotation Details (Read-Only) -->
                <div class="w-1/2 border-r pr-4">
                    <h2 class="text-lg font-semibold mb-4">Quotation Details</h2>

                    <!-- Quotation Fields in Read-Only Input Format -->
                    <div class="mb-4">
                        <label for="quotation_id" class="block text-gray-700">Quotation ID</label>
                        <input type="text" id="quotation_id" name="quotation_id" class="w-full px-3 py-2 border rounded-md"
                            value="{{ $quotation->id }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="name" class="block text-gray-700">Name</label>
                        <input type="text" id="name" name="name" class="w-full px-3 py-2 border rounded-md"
                            value="{{ $quotation->name }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-3 py-2 border rounded-md"
                            value="{{ $quotation->email }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="phone" class="block text-gray-700">Phone</label>
                        <input type="text" id="phone" name="phone" class="w-full px-3 py-2 border rounded-md"
                            value="{{ $quotation->phone ?? 'N/A' }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="company_name" class="block text-gray-700">Company</label>
                        <input type="text" id="company_name" name="company_name"
                            class="w-full px-3 py-2 border rounded-md" value="{{ $quotation->company_name }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700">Description</label>
                        <textarea id="description" name="description" class="w-full px-3 py-2 border rounded-md" rows="3" readonly>{{ $quotation->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="quantity_required" class="block text-gray-700">Quantity Required</label>
                        <input type="text" id="quantity_required" name="quantity_required"
                            class="w-full px-3 py-2 border rounded-md" value="{{ $quotation->quantity_required }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="desired_delivery_date" class="block text-gray-700">Desired Delivery Date</label>
                        <input type="text" id="desired_delivery_date" name="desired_delivery_date"
                            class="w-full px-3 py-2 border rounded-md"
                            value="{{ $quotation->desired_delivery_date ?? 'N/A' }}" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="additional_notes" class="block text-gray-700">Additional Notes</label>
                        <textarea id="additional_notes" name="additional_notes" class="w-full px-3 py-2 border rounded-md" rows="3"
                            readonly>{{ $quotation->additional_notes ?? 'N/A' }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label for="last_status" class="block text-gray-700">Last Status</label>
                        <input type="text" id="last_status" name="last_status" class="w-full px-3 py-2 border rounded-md"
                            value="{{ ucfirst($quotation->last_status) }}" readonly>
                    </div>
                </div>

                <!-- Right Half: Status and Notes Form -->
                <div class="w-1/2 pl-4">
                    <form action="{{ route('quotations.sendReply', $quotation->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="status" class="block font-medium">Status</label>
                            <select name="status" id="status" required class="w-full px-3 py-2 border rounded-md">
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block font-medium">Notes</label>
                            <textarea name="notes" id="notes" rows="6" class="w-full px-3 py-2 border rounded-md"></textarea>
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endsection
</x-layout>
