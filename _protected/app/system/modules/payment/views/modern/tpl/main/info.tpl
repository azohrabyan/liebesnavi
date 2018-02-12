<div class="center">

<table width=600 border=0 height=300 align=center>
<tr>
<td><a href="{{ $design->url('payment', 'main', 'membership','p=1') }}"><img class="img-circle" alt="paypal" src="/images/paypal.png" align="center"></a></td>
<td><a href="{{ $design->url('payment', 'main', 'membership','p=2') }}"><img class="img-circle" alt="sofort" src="/images/sofort.png" align="center"></td>
<td><a href="{{ $design->url('payment', 'main', 'membership','p=3') }}"><img class="img-circle" alt="paysafe" src="/images/paysafe.png" align="center"></td>
</tr>
</table>

    <p>
        <a class="btn btn-primary btn-md" href="{{ $design->url('payment', 'main', 'membership') }}">{lang 'Renew your membership'}</a>
    </p>
</div>
