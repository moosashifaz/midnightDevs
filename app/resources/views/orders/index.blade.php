<x-marketplace-layout>
    <div class="page-wrap">
    <x-ui.page-header title="My orders" />

    @if($orders->isEmpty())
        <x-ui.card class="p-10 text-center text-muraka-500">
            No orders yet.
            <a href="{{ route('home') }}" class="text-madi-600 hover:text-madi-700 font-medium">Browse the marketplace</a>.
        </x-ui.card>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="card-interactive block p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0">
                            <x-listing-image :listing="$order->listing" class="w-full h-full aspect-square" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-muraka-900 truncate">{{ $order->listing->title }}</div>
                            <div class="text-xs text-muraka-500">{{ $order->provider->business_name }} · {{ $order->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-right shrink-0">
                            <x-ui.price :usd="$order->amount_usd" :mvr="$order->amount_mvr" size="sm" />
                            @php
                                $statusClass = match($order->status) {
                                    'completed' => 'bg-ruh-100 text-ruh-800',
                                    'fulfilled' => 'bg-madi-100 text-madi-800',
                                    'paid' => 'bg-dhooni-100 text-dhooni-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    default => 'bg-moodhu-100 text-muraka-700',
                                };
                            @endphp
                            <span class="badge mt-1 {{ $statusClass }}">{{ $order->status }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
    </div>
</x-marketplace-layout>
