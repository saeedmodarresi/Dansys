<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ url('/js/jquery-3.6.0.min.js') }}"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ url('/css/colored-theme.min.css') }}">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
            integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

    <title>DAnSys</title>
</head>
<body id="app">

<section class="container mt-5 mb-5">

    <h6>Select Exchanges:</h6>
    <div class="exchanges mt-3 mb-4">
        @foreach ($exchanges as $exchange)
            <button class="btn btn-sm exchange @if($exchange['status'] == 1) btn-primary @endif"
                    data-id="{{ $exchange['id'] }}" data-status="{{ $exchange['status'] }}">
                {{ $exchange['name'] }}
            </button>
        @endforeach
    </div>

    <hr>

    <div class="get-feed mt-4 mb-4">
        <form action="" method="post">
            {{csrf_field()}}
            <button type="submit" class="btn btn-sm btn-dark" id="getFeed">Get Feed</button>
        </form>
    </div>

    <div class="d-flex">
        <h6>Last Update:</h6>
        <span id="date"></span>
    </div>

    <div id="logs" class="mt-4 mb-4">
        <ul class="list-group">
            @if($logs)
                @foreach ($logs as $log)
                    <li class="list-group-item">{{ $log }}</li>
                @endforeach
            @endif
        </ul>
    </div>

    <h6>Results:</h6>
    <table id="result" class="display mt-4 mb-4" style="width:100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>Percent</th>
            @foreach ($exchanges as $exchange)
                <th>{{ $exchange['name'] }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
            @if($data)
                @foreach($data as $datum)
                    <tr>
                        <td>{{$datum['name']}}</td>
                        <td>{{$datum['ave']}}</td>
                        @foreach($datum['exchanges'] as $item)
                            <td>{{$item}}</td>
                        @endforeach
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

</section>

<script src="{{ url('/js/growl-notification.min.js') }}"></script>
<script>
    var dt;

    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            }
        });

        dt = $('#result').DataTable();


        $(document).on('click', '.exchange', function (e) {

            e.preventDefault();

            let button = $(this);

            $.ajax({
                url: "{{ route('switch') }}",
                type: "POST",
                dataType: 'JSON',
                data: {
                    id: $(this).data('id'),
                    status: $(this).data('status'),
                },
                success: function (e) {

                    GrowlNotification.notify({
                        title: '',
                        description: e.success,
                        type: 'success',
                        position: 'top-right',
                        closeTimeout: 3000
                    });

                    button.toggleClass('btn-primary')
                }
            })

        })
    })

</script>
</body>
</html>
