<?php
class Pagination
{
    public static function get_offset($limit)
    {
        $limit = (int) $limit;
        $page_num = isset($_GET['pg']) ? (int) $_GET['pg'] : 1;
        $page_num = $page_num < 1 ? 1 : $page_num;
        $offset = ($page_num - 1) * $limit;

        return $offset;
    }

    public static function generate($number)
    {
        $number = (int)$number;
        $query_string = str_replace("url=", "", $_SERVER['QUERY_STRING']);


        $current_link = ROOT . $query_string;
        // add pg if not existing
        if (!strstr($query_string, "pg=")) {
            $current_link .= "&pg=1";
        }

        return preg_replace("/pg=[^&?=]+/", "pg=" . $number, $current_link);
    }

    public static function links()
    {
        $links = (object)[];

        $links->prev = "";
        $links->next = "";
        $query_string = str_replace("url=", "", $_SERVER['QUERY_STRING']);

        $page_number = isset($_GET['pg']) ? (int)$_GET['pg'] : 1;
        $page_number = $page_number < 1 ? 1 : $page_number;

        $next_page = $page_number + 1;
        $prev_page = ($page_number > 1) ? $page_number - 1 : 1;

        $current_link = ROOT . $query_string;
        // add pg if not existing
        if (!strstr($query_string, "pg=")) {
            $current_link .= "&pg=1";
        }

        $links->prev = preg_replace("/pg=[^&?=]+/", "pg=" . $prev_page, $current_link);
        $links->next = preg_replace("/pg=[^&?=]+/", "pg=" . $next_page, $current_link);
        $links->current = $page_number;

        return $links;
    }

    public static function show_link()
    {
        $max = self::links()->current + 5;
        $cur = self::links()->current;
?>
        <div class="text-center">
            <ul class="pagination">
                <?php if ($cur > 1) : ?>
                    <li><a href="<?= self::links()->prev ?>">Prev</a></li>
                <?php endif; ?>
                <?php for ($i = $cur; $i < $max; $i++) : ?>
                    <li class="<?= (self::links()->current == $i) ? 'active' : '' ?>"><a href="<?= self::generate($i) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <!-- <li><a href="">&raquo;</a></li> -->
                <li><a href="<?= self::links()->next ?>">Next</a></li>
            </ul>
        </div>
<?php }
}
