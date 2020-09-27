
<htmlpageheader name="page-header">
    Price List of Detpak web Site
</htmlpageheader>

<p><strong>Date : </strong>  {{ $date }}</p>

<table class="table table-bordered" style="background-color: white">
    <thead>
    <tr>
        <td width="150">SKU</td>
        <td width="400">Title</td>
        <td width="150">Price (AED)</td>
    </tr>
    </thead>
    <tbody>
    @foreach($items as $item)
        <tr>
            <td><span>{{ $item->sku }}</span></td>
            <td><span style="font-family: Tahoma, Helvetica, Arial; font-size: 12px;">{{ $item->title }}</span></td>
            <td><span>{{ $item->price }}</span></td>
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