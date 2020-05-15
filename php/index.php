<?php

function getItemTitleByIndexNumber($indexNumber)
{
    switch ($indexNumber) {
        case 1:
            return 'First item';
        case 2:
            return 'Second item';
        case 3:
            return 'Third item';
        default:
            return 'Item '.$indexNumber;
    }
}

function getItemsBetweenRange($rangeStart, $rangeEnd)
{
    $result = [];
    for ($i = $rangeStart; $i <= $rangeEnd; ++$i) {
        $result[] = [
            'id' => $i,
            'title' => getItemTitleByIndexNumber($i),
            'description' => 'This is the item number '.$i,
        ];
    }

    return $result;
}

function getPagination($currentPage, $boundaries, $around, $totalPages)
{
    $leftBoundaries = $boundaries >= 1 ? range(1, $boundaries) : [];
    $leftAround = $around >= 1 ? range($currentPage - $around, $currentPage - 1) : [];
    $rightAround = $around >= 1 ? range($currentPage + 1, $currentPage + $around) : [];
    $rightBoundaries = $boundaries >= 1 ? range(($totalPages - $boundaries) + 1, $totalPages) : [];

    $result = array_filter(
        array_unique(
            array_merge($leftBoundaries, $leftAround, [$currentPage], $rightAround, $rightBoundaries),
            SORT_NUMERIC
        ),
        function ($page) use ($totalPages) { return $page > 0 && $page <= $totalPages; }
    );
    sort($result);

    return $result;
}

function validNumeric($var)
{
    return !empty($var) && is_numeric($var);
}

// defaults to 20 pages:
$total_pages = validNumeric($_GET['total_pages']) ? $_GET['total_pages'] : 20;
// lets define a minimum of 1 page for the total
if ($total_pages < 1) {
    $total_pages = 1;
}

// defaults to page 1:
$current_page = validNumeric($_GET['current_page']) ? $_GET['current_page'] : 1;
// no overflow or underflows for the current page:
if ($current_page > $total_pages) {
    $current_page = $total_pages;
}
if ($current_page < 1) {
    $current_page = 1;
}

// defaults to 3 items per page:
$page_size = validNumeric($_GET['page_size']) ? $_GET['page_size'] : 3;

// boundaries defaults to 2:
// allows the boundaries to be set to 0
$boundaries = '0' === $_GET['boundaries'] || validNumeric($_GET['boundaries']) ? $_GET['boundaries'] : 2;

// around defaults to 2:
// allows the around to be set to 0
$around = '0' === $_GET['around'] || validNumeric($_GET['around']) ? $_GET['around'] : 2;

$items = getItemsBetweenRange((($current_page * $page_size) - $page_size) + 1, $current_page * $page_size);
$pagination = getPagination($current_page, $boundaries, $around, $total_pages);

?><!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Pagination Challenge in PHP</title>

        <link rel="stylesheet" href="styles.css" />
    </head>
    <body>
        <div id="container">
            <div id="container-header">
                <h1>Pagination Challenge</h1>
                <a href="#" id="change_configuration">Change configurations</a>

                <div id="configurations">
                    <h3>Configurations</h3>
                    <form>
                        <div class="item">
                            <label for="current_page">
                                Current page:
                            </label>
                            <br/>
                            <input type="number" name="current_page" value="<?php echo $current_page; ?>"/>
                        </div>
                        <div class="item">
                            <label for="total_pages">
                                Total pages:
                            </label>
                            <br/>
                            <input type="number" name="total_pages" value="<?php echo $total_pages; ?>"/>
                        </div>
                        <div class="item">
                            <label for="page_size">
                                Total items per page:
                            </label>
                            <br/>
                            <input type="number" name="page_size" value="<?php echo $page_size; ?>"/>
                        </div>
                        <div class="item">
                            <label for="boundaries">
                                Boundaries:
                            </label>
                            <br/>
                            <input type="number" name="boundaries" value="<?php echo $boundaries; ?>"/>
                        </div>
                        <div class="item">
                            <label for="around">
                                Around:
                            </label>
                            <br/>
                            <input type="number" name="around" value="<?php echo $around; ?>"/>
                        </div>
                        <button type="submit" id="save_configurations">Save</button>
                    </form>
                </div>
            </div>
            <div id="container-content">
                <?php foreach ($items as $item) { ?>
                    <div class="container-content-item">
                        <h2><?php echo $item['title']; ?></h2>
                        <p><?php echo $item['description']; ?></p>
                        <a href="#item<?php echo $item['id']; ?>">Open</a>
                    </div>
                <?php } ?>
            </div>
            <div id="container-footer">
                <div id="pagination">
                    <?php for ($i = 0; $i <= count($pagination); ++$i) { ?>
                            <a 
                            <?php echo $pagination[$i] == $current_page ? "style='font-size: 20px; text-decoration: none'" : ''; ?> 
                            href="/?current_page=<?php echo $pagination[$i]; ?>&boundaries=<?php echo $boundaries; ?>&around=<?php echo $around; ?>&page_size=<?php echo $page_size; ?>&total_pages=<?php echo $total_pages; ?>">
                                <?php echo $pagination[$i]; ?>
                            </a>
                            <?php if ($pagination[$i + 1] && $pagination[$i] + 1 < $pagination[$i + 1]) { ?>
                                ... 
                            <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>

        <script type="text/javascript">

            document.getElementById('change_configuration').onclick = function(e){
                const configBox = document.getElementById('configurations');
                if(configBox.style.display == 'block'){
                    configBox.style.display = 'none';
                } else {
                    configBox.style.display = 'block';
                }
            };

        </script>
    </body>
</html>