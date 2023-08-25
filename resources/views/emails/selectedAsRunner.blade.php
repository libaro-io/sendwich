<x-mail::message>
# Dear {{ $runner->name }},
## You are the chosen one to pick up lunch today.

<x-mail::table>
| For       | Product         | Quantity  | Price |
| :------------- |:-------------| --------:|---------: |
@foreach($orders as $order)
    | {{ $order->user->name }} | {{ $order->product->name }} @if($order->comment !== '') ({{ $order->comment }}) @endif | {{ $order->quantity }} | €{{ number_format($order->total, 2) }} |
@endforeach
    | | | **Total** | €{{ number_format($orders->sum('total'), 2) }} |
</x-mail::table>
<br />
Hurry up! We're are starving here.
</x-mail::message>
