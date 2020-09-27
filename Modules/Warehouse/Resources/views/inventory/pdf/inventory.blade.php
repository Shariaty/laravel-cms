<style>
    .print-farsi-text{
        font-family: Tahoma, Helvetica, Arial; font-size: 12px;
    }
    .center-text{
        text-align: center;
    }
    .left-text{
        text-align: left;
    }
    .text-success{
        color: green;
    }
    .text-danger{
        color: red;
    }
    .text-warning{
        color: orange;
    }
    .text-info{
        color: gray;
    }
</style>

<h3 class="center-text print-farsi-text">
    لیست موجودی انبار
</h3>

<h5 class="center-text print-farsi-text"><strong>تاریخ  : </strong>  {{ $date }}</h5>

<table class="table table-bordered" style="background-color: white">
    <thead>
    <tr>
        <th class="center-text print-farsi-text" width="120">کد کالا</th>
        <th class="center-text print-farsi-text" width="250">عنوان</th>
        <th class="center-text print-farsi-text" width="100" >واحد شمارش</th>
        <th class="center-text print-farsi-text" width="60">وارده</th>
        <th class="center-text print-farsi-text" width="60" >صادره</th>
        <th class="center-text print-farsi-text" width="60" >موجودی</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)

        @php
            $finalLastTitle = '';
            if ($item->final_title) {
                $finalLastTitle .= $item->final_title;
            }

            if ($item->option_1){
                $finalLastTitle .= ' - '.$attributes[$item->option_1];
            }
            if ($item->option_2){
                $finalLastTitle .= ' - '.$attributes[$item->option_2];
            }
        @endphp

        <tr>
            <td class="left-text">{{ $item->final_sku }}</td>
            <td class="center-text print-farsi-text">{{ $finalLastTitle }}</td>
            <td class="center-text print-farsi-text">{{ $units[$item->final_unit] }}</td>
            <td class="center-text print-farsi-text {{generateTextColor($item->totalBuy)}}">{{ round($item->totalBuy , 3) }}</td>
            <td class="center-text print-farsi-text {{generateTextColor($item->totalSell)}}">{{ round($item->totalSell , 3) }}</td>
            <td class="center-text print-farsi-text {{generateTextColor($item->stock)}}">{{ round($item->stock , 3) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<htmlpagefooter name="page-footer">
    <span style="font-family: Tahoma, Helvetica, Arial; font-size: 12px;">{PAGENO} صفحه شماره</span>
</htmlpagefooter>


<style>
    @page {
        header: page-header;
        footer: page-footer;
    }
</style>