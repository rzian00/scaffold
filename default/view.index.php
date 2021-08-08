<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf_token" content="{{csrf_token()}}">
    <title>@{TITLE}</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/lumen/bootstrap.min.css" integrity="sha384-iqcNtN3rj6Y1HX/R0a3zu3ngmbdwEa9qQGHdkXwSRoiE+Gj71p0UNDSm99LcXiXV" crossorigin="anonymous">
</head>
<body>

    <div class="container">
        <div class="module">
            <div class="content-search mb-2 d-flex justify-content-between">
                <div>
                    <button type="button" class="btn btn-secondary" id="btn-create">Create</button>
                </div>

                <div class="input-group" style="width: 400px">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Search" aria-describedby="btn-search">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-secondary" id="btn-search">Search</button>
                    </div>
                </div>
            </div>

            <table class="table table-striped">
                <thead>
                <tr>@{COLUMNS}
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(function() {

        });
    </script>
</body>
</html>