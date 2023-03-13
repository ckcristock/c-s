<style>
    .page-content {
        width: 750px;
    }

    .row {
        display: inlinie-block;
        width: 750px;
    }

    .td-header {
        font-size: 15px;
        line-height: 20px;
    }

    .titular {
        font-size: 11px;
        text-transform: uppercase;
        margin-bottom: 0;
    }
</style>
@include('components/cabecera', [$company, $datosCabecera, $image])

{!!$content!!}
