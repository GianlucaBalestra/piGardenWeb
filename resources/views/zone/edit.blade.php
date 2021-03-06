@extends('backpack::layout')

@section('body_class', 'zone-edit')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('pigarden.zone') . ' ' . (is_null($zone) ? '' : $zone->name_stripped) }}<small></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('admin') }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('pigarden.zone')  . ' ' . (is_null($zone) ? '' : $zone->name_stripped)}}</li>
      </ol>
    </section>
@endsection

@section('before_styles')
    <link href="{{ asset("vendor/adminlte/plugins/select2/select2.min.css") }}" rel="stylesheet">
@endsection

@section('after_styles')
    <link href="{{ asset("css/pigarden.css") }}" rel="stylesheet">
    <link href="{{ asset('css/icheck/line/blue.css') }}" rel="stylesheet">
@endsection

@section('content')

    @if(!empty($error->description))
    <div class="callout callout-danger lead">
        <h4>PiGarden server error</h4>
        <pre><?php print_r($error) ?></pre>
    </div>
    @endif

    @include('_partials.errors')

    @if(!empty($zone))
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            @include('_partials.zone', ['zone' => $zone, 'force' => true])
        </div>
    </div>

    {!! Form::open(['route' => ['cron.put', $zone->name]]) !!}
    {!! Form::hidden('type', '', ['id' => 'cron_type'])!!}
    <div class="row">
        @foreach(['open', 'close'] as $type)
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="box box-primary box-cron
             box-info">
                <div class="box-header with-border text-center">
                    <div class="box-title">{{ trans('pigarden.cron.'.$type.'_title') }}</div>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="table-{{$type}}-cron" class="table table-cron table-striped table-hover table-borderless no-margin with-tools">
                            <tbody>
                                @if( !is_null(old($type)) && false )
                                    <div><pre><?php print_r(old($type))?></pre></div>
                                @else
                                @foreach( (!is_null(old($type)) ? old($type) : ( !is_null(old('type')) ? array() : $cron[$type]) ) as $k => $item)
                                <tr id="{{$type}}-row-{{$k}}" class="{{$type}}-row" data-cronrow="{{$k}}">
                                    <td class="tools">
                                        <ul class="cron-item-text"></ul>
                                        {{-- $item['string']  --}}
                                        {!! Form::hidden("{$type}[$k][min]", (is_array($item['min']) ? implode(',',$item['min']) : $item['min']), ['id'=>"$type-min-".$k]) !!}
                                        {!! Form::hidden("{$type}[$k][hour]", (is_array($item['hour']) ? implode(',',$item['hour']) : $item['hour']), ['id'=>"$type-hour-".$k]) !!}
                                        {!! Form::hidden("{$type}[$k][dom]", (is_array($item['dom']) ? implode(',',$item['dom']) : $item['dom']), ['id'=>"$type-dom-".$k]) !!}
                                        {!! Form::hidden("{$type}[$k][month]", (is_array($item['month']) ? implode(',',$item['month']) : $item['month']), ['id'=>"$type-month-".$k]) !!}
                                        {!! Form::hidden("{$type}[$k][dow]", (is_array($item['dow']) ? implode(',',$item['dow']) : $item['dow']), ['id'=>"$type-dow-".$k]) !!}
                                        <div class="tools-wrp">
                                            <a href="#" class="{{$type}}-cron-modify" id="{{$type}}-cron-modify-{{$k}}" data-toggle="modal" data-target="#cronModal" data-crontype="{{$type}}" data-cronrow="{{$k}}">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </a>
                                            <a href="#" class="{{$type}}-cron-delete" id="{{$type}}-cron-delete-{{$k}}" data-toggle="confirmation" data-title="Open Google?">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                            <tfoot>
                                <tr><td><i>{{trans('cron.no_scheduling')}}</i></td></tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="box-footer clearfix" style="display: block;">
                        <button type="submit" class="btn btn-primary pull-left" onclick="$('#cron_type').val('{{$type}}')"><i class="glyphicon glyphicon-save"></i> {{trans('cron.save')}}</button>
                        <a hred="#" class="btn btn-default pull-right" data-toggle="modal" data-target="#cronModal" data-crontype="{{$type}}" data-cronrow=""><i class="fa fa-plus"></i> {{trans('cron.add')}}</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    {!! Form::close() !!}




    <!-- Modal -->
    <div class="modal fade" id="cronModal" tabindex="-1" role="dialog" aria-labelledby="cronModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="cronModalLabel">Cron schedule</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
                {{ Form::label('cron-min', trans('cron.min.title')) }}
                <div>{{ Form::select('cron-min', \App\CronHelper::getMinSelectItemArray('min-*'), null, ['multiple' => 'multiple', 'class' => 'form-control', 'style' => 'width:100%;']) }}</div>
            </div>
            <div class="form-group">
                {{ Form::label('cron-hour', trans('cron.hour.title')) }}
                <div>{{ Form::select('cron-hour', \App\CronHelper::getHourSelectItemArray(), null, ['multiple' => 'multiple', 'class' => 'form-control', 'style' => 'width:100%;']) }}</div>
            </div>
            <div class="form-group">
                {{ Form::label('cron-dom', trans('cron.dom.title')) }}
                <div>{{ Form::select('cron-dom', \App\CronHelper::getDomSelectItemArray(), null, ['multiple' => 'multiple', 'class' => 'form-control', 'style' => 'width:100%;']) }}</div>
            </div>
            <div class="form-group">
                {{ Form::label('cron-month', trans('cron.month.title')) }}
                <div>{{ Form::select('cron-month', \App\CronHelper::getMonthSelectItemArray(), null, ['multiple' => 'multiple', 'class' => 'form-control', 'style' => 'width:100%;']) }}</div>
            </div>
            <div class="form-group">
                {{ Form::label('cron-dow', trans('cron.dow.title')) }}
                <div>{{ Form::select('cron-dow', \App\CronHelper::getDowSelectItemArray(), null, ['multiple' => 'multiple', 'class' => 'form-control', 'style' => 'width:100%;']) }}</div>
            </div>

            <input name="cron-type" type='hidden' value="" />
            <input name="cron-row" type='hidden' value="" />

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('pigarden.cancel')}}</button>
            <button type="button" class="btn btn-primary" id="cron-modal-confirm">{{trans('pigarden.confirm')}}</button>
          </div>
        </div>
      </div>
    </div>

    @endif

@endsection

@section('after_scripts')
    <script src="{{ asset('js/icheck.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/base.js') }}"></script>
    <script src="{{ asset('js/backend.js') }}"></script>
    <script>
    $(document).ready(function(){

        $('.btn-zone, .btn-zone-open-in, .btn-zone-open-in-cancel').click(function(e){
            var btn = $(this);
            var id;
            if(btn.hasClass('btn-zone-open-in') || btn.hasClass('btn-zone-open-in-cancel')){
                id = $(btn.parents('.btn-group-zone').find('.btn-zone')).prop('id').replace('btn-zone-', '');
            } else {
                id = $(btn).prop('id').replace('btn-zone-', '');
            }

            var url = btn.attr('href');
            if((url.indexOf('/play/')>=0 || url.indexOf('/play_in/')>=0 ) && $($('.force_open').get(0)).prop('checked')){
                url = url + '/force';
            }
            $.ajax({
                type : "GET",
                url : url,
                dataType: 'json',
                beforeSend: function(jqXHR) {
                    $('#box-zone-'+id).append('<div class="overlay"><i class="fa fa-refresh fa-spin"></i></div>');
                },
                success: function (data, textStatus, jqXHR) {
                    console.log(data);
                    updateZones(data);
                    updateNotify(data);
                },
                error: function( jqXHR, textStatus, errorThrown ){
                    callBackAjaxError(jqXHR, textStatus, errorThrown);
                },
                complete: function(jqXHR, textStatus){
                    $('#box-zone-'+id+' .overlay').remove();
                }
            });

            e.preventDefault();
        });

        $('input.icheck').each(function(){
            var self = $(this),
            label = self.next(),
            label_text = label.text();

            label.remove();
            self.iCheck({
                checkboxClass: 'icheckbox_line-blue',
                radioClass: 'iradio_line-blue',
                insert: '<div class="icheck_line-icon"></div>' + label_text
            });
        });

        // Elimina una schedulazione
        $('.open-cron-delete, .close-cron-delete').click(callBackDeleteCronSchedule);
        function callBackDeleteCronSchedule(e){
            if(confirm("Confermi l'eliminazione")){
                var self = $(this);
                var type = self.hasClass('open-cron-delete') ? 'open' : 'close';
                var id = self.attr('id').replace(type+'-cron-delete-','');
                $('#'+type+'-row-'+id).fadeOut(function(){
                    $(this).remove();
                    cronShowHideTableFoot($('#table-'+type+'-cron'));
                });
            }
            e.preventDefault();
        }

        // Prepara i campi nella finestra di editing schedulazione
        $('#cronModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var row = button.data('cronrow');
            var type = button.data('crontype');
            var min;
            var hour;
            var dom;
            var month;
            var dow;

            if(row !== ""){
                min = $('#'+type+'-min-'+row).val().split(',');
                hour = $('#'+type+'-hour-'+row).val().split(',');
                dom = $('#'+type+'-dom-'+row).val().split(',');
                month = $('#'+type+'-month-'+row).val().split(',');
                dow = $('#'+type+'-dow-'+row).val().split(',');
            } else {
                min = ['min-1'];
                hour = ['hour-22'];
                dom = ['dom-*'];
                month = ['month-*'];
                dow = ['dow-*'];
            }

            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this);
            modal.find('.modal-body input[name="cron-type"]').val(type);
            modal.find('.modal-body input[name="cron-row"]').val(row);

            $.each({'min': min, 'hour': hour, 'dom': dom, 'month': month, 'dow': dow}, function(i, v){
                $.each($('#cron-'+i+' option'), function(io, o){
                    $(o).prop('selected', ($.inArray($(o).val(), v) >= 0 ? true : false) );
                });
            });

            $('#cron-min, #cron-hour, #cron-dom, #cron-month, #cron-dow').select2();

        });

        // Conferma l'edit di una schedulazione
        $("#cron-modal-confirm").click(function(){
            var type = $('#cronModal input[name="cron-type"]').val();
            var row = $('#cronModal input[name="cron-row"]').val();
            if(row == ""){
                var ids = $('.'+type+'-row').map(function(){return parseInt($(this).data('cronrow')) });
                var row = ids.length > 0 ? Math.max.apply(null, ids)+1 : 0;
                $('#table-'+type+'-cron tbody').append(
                    '<tr id="'+type+'-row-'+row+'" class="'+type+'-row" data-cronrow="'+row+'">' +
                        '<td class="tools">' +
                            '<ul class="cron-item-text"></ul>' +
                            '<input id="'+type+'-min-'+row+'" name="'+type+'['+row+'][min]" type="hidden" value="">' +
                            '<input id="'+type+'-hour-'+row+'" name="'+type+'['+row+'][hour]" type="hidden" value="">' +
                            '<input id="'+type+'-dom-'+row+'" name="'+type+'['+row+'][dom]" type="hidden" value="dom-*">' +
                            '<input id="'+type+'-month-'+row+'" name="'+type+'['+row+'][month]" type="hidden" value="">' +
                            '<input id="'+type+'-dow-'+row+'" name="'+type+'['+row+'][dow]" type="hidden" value="">' +
                            '<div class="tools-wrp">' +
                                '<a href="#" class="'+type+'-cron-modify" id="'+type+'-cron-modify-'+row+'" data-toggle="modal" data-target="#cronModal" data-crontype="'+type+'" data-cronrow="'+row+'">' +
                                    '<i class="fa fa-pencil" aria-hidden="true"></i>' +
                                '</a>' +
                                '<a href="#" class="'+type+'-cron-delete" id="'+type+'-cron-delete-'+row+'">' +
                                    '<i class="fa fa-trash" aria-hidden="true"></i>' +
                                '</a>' +
                            '</div>' +
                        '</td>' +
                    '</tr>'
                );
                $('#'+type+'-row-'+row+' .'+type+'-cron-delete').click(callBackDeleteCronSchedule);
            }
            $('#'+type+'-min-'+row).val( ($('#cron-min').val() ? $('#cron-min').val().join(',') : 'min-1') );
            $('#'+type+'-hour-'+row).val( ($('#cron-hour').val() ? $('#cron-hour').val().join(',') : 'hour-*') );
            $('#'+type+'-dom-'+row).val( ($('#cron-dom').val() ? $('#cron-dom').val().join(',') : 'dom-*') );
            $('#'+type+'-month-'+row).val( ($('#cron-month').val() ? $('#cron-month').val().join(',') : 'month-*') );
            $('#'+type+'-dow-'+row).val( ($('#cron-dow').val() ? $('#cron-dow').val().join(',') : 'dow-*') );

            updateCronItemText(type, row);
            cronShowHideTableFoot($('#table-'+type+'-cron'));

            $("#cronModal").modal('hide');
        });

        // Visualizza la descrizione di ogni schedulazioni
        $(".open-row, .close-row").each(function(i,o){
            var type = $(o).hasClass('open-row') ? 'open' : 'close';
            var row = $(o).attr('id').replace(type+'-row-','');
            updateCronItemText(type, row);
        });

        $('.table-cron').each(function(i,o){
            cronShowHideTableFoot(o);
        });

    });

    // Mostra o nasconde il footer della tabella di schedulazione
    function cronShowHideTableFoot(table){
        if($(table).find('tbody tr').length > 0){
            $(table).find('tfoot').hide();
        }else{
            $(table).find('tfoot').show();
        }
    }

    function updateCronItemText(type, row) {
        var min = $('#'+type+'-min-'+row).val().split(',');
        var hour = $('#'+type+'-hour-'+row).val().split(',');
        var dom = $('#'+type+'-dom-'+row).val().split(',');
        var month = $('#'+type+'-month-'+row).val().split(',');
        var dow = $('#'+type+'-dow-'+row).val().split(',');
        var html = '';

        $.each({'min': min, 'hour': hour, 'dom': dom, 'month': month, 'dow': dow}, function(i, v){
            $.each(v, function(ia, va){
                html += '<li class="'+i+'">'+$('#cron-'+i+' option[value="'+va+'"]').text()+'</li>';
                if (va == i+'-*'){
                    return false;
                }
            });
        });

        $(" #"+type+"-row-"+row+" td .cron-item-text").html(html);
    }

    </script>
@endsection
