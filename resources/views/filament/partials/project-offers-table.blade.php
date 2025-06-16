<table class="filament-tables-table w-full border-collapse">
    <thead>
        <tr>
            <th class="px-4 py-2 text-left">Offer ID</th>
            <th class="px-4 py-2 text-left">User</th>
            <th class="px-4 py-2 text-left">Price</th>
            <th class="px-4 py-2 text-left">Deadline</th>
            <th class="px-4 py-2 text-left">Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($offers as $offer)
            <tr class="border-t">
                <td class="px-4 py-2">{{ $offer->id }}</td>
                <td class="px-4 py-2">{{ $offer->user->name }}</td>
                <td class="px-4 py-2">${{ number_format($offer->price, 2) }}</td>
                <td class="px-4 py-2">{{ $offer->deadline?->format('Y-m-d H:i') ?? '-' }}</td>
                <td class="px-4 py-2">{{ ucfirst($offer->status->value) }}</td>
            </tr>
        @empty
            <tr>
                <td class="px-4 py-2 text-center" colspan="5">No offers yet.</td>
            </tr>
        @endforelse
    </tbody>
</table>
