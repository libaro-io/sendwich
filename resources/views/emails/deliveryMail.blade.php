<div>
    <table>
        @foreach($orders as $order)
            <tr>
                <td>
                    <p>{{ $order->user->name }}</p>
                </td>
                <td>
                    <p>{{ $order->product->name }}</p>
                </td>
                <td>
                    <p>{{ $order->product->price }}</p>
                </td>
            </tr>
        @endforeach
    </table>
</div>
