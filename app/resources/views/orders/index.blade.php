<x-marketplace-layout>
    <h1 class="text-2xl font-bold mb-6">My orders</h1>

    @if($orders->isEmpty())
        <div class="rounded-2xl bg-gray-50 dark:bg-gray-800 p-10 text-center text-gray-500">
            No orders yet. <a href="{{ route('home') }}" class="text-cyan-600 hover:underline">Browse the marketplace</a>.
        </div>
    @else
        <div class="space-y-3">
            @foreach($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="block rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-4 hover:shadow-md transition">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-cyan-100 to-blue-100 rounded-xl flex items-center justify-center text-2xl">
                            @switch($order->listing->category)
                                @case('eat') 🍽️ @break
                                @case('wash') 🧺 @break
                                @case('buy') 🛍️ @break
                                @case('experience') ⛵ @break
                            @endswitch
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold">{{ $order->listing->title }}</div>
                            <div class="text-xs text-gray-500">{{ $order->provider->business_name }} · {{ $order->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold">MVR {{ number_format($order->amount_mvr, 2) }}</div>
                            <span class="text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-full
                                @switch($order->status)
                                    @case('completed') bg-emerald-100 text-emerald-700 @break
                                    @case('fulfilled') bg-blue-100 text-blue-700 @break
                                    @case('paid') bg-amber-100 text-amber-700 @break
                                    @case('cancelled') bg-red-100 text-red-700 @break
                                    @default bg-gray-100 text-gray-700
                                @endswitch
                            ">{{ $order->status }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
</x-marketplace-layout>
