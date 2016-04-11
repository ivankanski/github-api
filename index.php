<?php

require_once 'db_connect.php';
require_once 'Github.php';

$gh = new Github();

// Could sort by something other than stars, as defined in class member constants.
$gh->sort       = $gh::SORT_STARS;

// Param to change language search option.
$gh->language   = 'php';

// Number of page results, maximum 100 as defined by the GitHub API.
$gh->per_page   = 100;

// Minimum stars the projects must have to be displayed in page results. Since we're showing just the top 100 sorted by descending star count, this value is not required. Setting to '0' will simply just show the "Top 100", or setting to '10000' will show "Top 100 over 10,000 stars". If there are not 100 (or $gh->per_page) number of results in criteria only the qualified listings are shown.
$gh->min_stars  = 0;

$subhead =  'Top '.$gh->per_page;
if($gh->min_stars > 0) $subhead.=' over '.number_format($gh->min_stars, 0).' stars';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="GitHub PHP Projects">
    <meta name="author" content="Ivan Kanski">
    <title>GitHub PHP Projects</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/grid.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <script src="js/jquery-1.12.3.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/ui.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<header>
    <h1>GitHub PHP Projects</h1>
    <h2><?=$subhead?></h2>
</header>
<div class="container">
    <div class="row">
        <div class="list-group">
<?php
$gh->get_projects();

$pdo = $dblink->prepare('
    INSERT INTO repos (repo_id, repo_name, repo_url, repo_created, repo_pushed, repo_descript, repo_stars)
    VALUES (:repo_id, :repo_name, :repo_url, :repo_created, :repo_pushed, :repo_descript, :repo_stars)
    ON DUPLICATE KEY UPDATE
    repo_name     = VALUES(repo_name),
    repo_url      = VALUES(repo_url),
    repo_pushed   = VALUES(repo_pushed),
    repo_descript = VALUES(repo_descript),
    repo_stars    = VALUES(repo_stars)
');

$ct=0;
foreach($gh->iterate(json_decode($gh->response)->items) as $proj):
    $ct++;
    $pdo->execute(array(
        ':repo_id'      => $proj->id,
        ':repo_name'    => $proj->full_name,
        ':repo_url'     => $proj->html_url,
        ':repo_created' => $proj->created_at,
        ':repo_pushed'  => $proj->updated_at,
        ':repo_descript'=> $proj->description,
        ':repo_stars'   => $proj->stargazers_count
        ));
?>
    <a href="#" class="list-group-item row<?=$ct%2?>">

       <span class="starbg num"><?=$ct?></span> <span class="title"><?=$proj->full_name?></span>
        <span class="meta starbg">&starf;<?=$proj->stargazers_count?></span>
        <div class="detail_box">
            <table>
            <tr>
                <td colspan="2"><a class="detail_url" href="<?=htmlspecialchars($proj->html_url, ENT_QUOTES)?>" target="_blank"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> <?=htmlspecialchars($proj->html_url, ENT_QUOTES)?></a></td>
            </tr>
            <tr class="descr">
                <td colspan="2"><?=htmlspecialchars($proj->description, ENT_QUOTES)?></td>
            </tr>
            <tr class="meta">
                <td>Repo Id:</td>
                <td><?=htmlspecialchars($proj->id, ENT_QUOTES)?></td></tr>
            <tr class="meta">
                <td>Created:</td>
                <td><?=htmlspecialchars($proj->created_at, ENT_QUOTES)?></td>
            </tr>
            <tr class="meta">
                <td>Last Pushed:</td>
                <td><?=htmlspecialchars($proj->updated_at, ENT_QUOTES)?></td>
            </tr>
            </table>
        </div>
    </a>
<?php endforeach; ?>
        </div>
    </div>
</div>
</body>
</html>
