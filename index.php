<?php
set_time_limit(0);

function get($url)
{
    $ch = curl_init ();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
    if ($statusCode == 200) {
        return $response;
    }

    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = $_POST["url"] ?? null;
    $start = $_POST["start"] ?? null;
    $end = $_POST["end"] ?? null;

    if (!$url or !$start or !$end) {
        $error = "Tüm alanlar doldurulmalı...";
    }

    if (!is_numeric($start) or !is_numeric($end)) {
        $error = "Başlangıç ve bitiş değerleri numeric olmalıdır. start: " . $start . " end: " . $end;
    }

    if (!isset($error)) {
        $start = (int)$start;
        $end = (int)$end;

        $finding = [];
        for ($i = $start; $i <= $end; $i++) {
            $getResult = get(str_replace("{page}", $i, $url));
            $getResult = json_decode($getResult, true);

            if (isset($getResult["link"])) {
                $finding = array_merge($finding, [$getResult["link"] => $i]);
            }
        }
    }

}

?>

<!doctype html>
<html lang="en">
<head>
    <title>Scraper</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
          integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>


<div class="container">
    <div class="row mt-2">
        <div class="col-md-12">
            <?php
                if (isset($error)) {
                    echo '<div class="alert alert-danger" role="alert"><strong>'.$error.'</strong></div>';
                }
                if (isset($success)) {
                    echo '<div class="alert alert-success" role="alert"><strong>'.$success.'</strong></div>';
                }
            ?>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Site Adresi:</label>
                            <input type="url" class="form-control" name="url" id="url" aria-describedby="url" value="https://www.homedit.com/wp-json/wp/v2/categories/{page}" required>
                            <small id="url" class="form-text text-muted">Değişkenin geleceği alanı {page} olarak yazınız.</small>
                        </div>
                        <div class="form-group">
                            <label for="start">Başlangıç Değeri</label>
                            <input type="number" class="form-control" name="start" id="start" min="1" value="1">
                        </div>
                        <div class="form-group">
                            <label for="end">Bitiş Değeri</label>
                            <input type="number" class="form-control" name="end" id="end" min="1" value="100">
                        </div>
                        <button type="submit" class="btn btn-primary" onclick="this.form.submit(); this.disabled=true; this.innerText ='Başlatıldı…';">Başlat</button>
                    </form>
                </div>
            </div>
        </div>

        <?php
            if (isset($finding)) {
                echo '<div class="col-md-12 mt-3"><hr><h3>Sonuçlar:</h3>';
                foreach ($finding as $url => $id) {
                    echo $url . " id:" . $id . "<br>";
                }
                echo '</div>';
            }
        ?>
    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>